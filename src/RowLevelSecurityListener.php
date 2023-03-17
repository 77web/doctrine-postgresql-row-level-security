<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\SchemaCreateTableColumnEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

class RowLevelSecurityListener implements EventSubscriber
{
    private const RLS_VISITED_OPTION_NAME = 'rowLevelSecurityConfigured';

    public function getSubscribedEvents(): iterable
    {
        return [
            ToolEvents::postGenerateSchemaTable,
            Events::onSchemaCreateTableColumn,
        ];
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args): void
    {
        $rlsAttributes = $args->getClassMetadata()->reflClass->getAttributes(RowLevelSecurity::class);
        if (count($rlsAttributes) === 0) {
            return;
        }
        $table = $args->getClassTable();
        $table->addOption(RowLevelSecurityConfig::RLS_OPTION_NAME, $rlsAttributes[array_key_first($rlsAttributes)]->getArguments());
    }

    public function onSchemaCreateTableColumn(SchemaCreateTableColumnEventArgs $args): void
    {
        // 本当はonSchemaCreateTableにフックしたいがDoctrine側の事情でCreateTableEventArgsのsqlはcreate tableのSQLを含めないといけないのでデフォルトのcreate tableのSQLにアドオンできるColumnのイベントを利用
        if (!$args->getTable()->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME) || $args->getTable()->hasOption(self::RLS_VISITED_OPTION_NAME)) {
            return;
        }
        $table = $args->getTable();
        $rlsOptions = $table->getOption(RowLevelSecurityConfig::RLS_OPTION_NAME);
        $policyName = $rlsOptions['name'] ?? sprintf('%s_policy', $table->getName());
        $tableName = $table->getQuotedName($args->getPlatform());
        $roleName = $rlsOptions['role'];
        $using = $rlsOptions['using'];

        $sqlFactory = new RowLevelSecuritySqlFactory();
        foreach ($sqlFactory->createEnableSqls($policyName, $tableName, $roleName, $using) as $sql) {
            $args->addSql($sql);
        }

        $table->addOption(self::RLS_VISITED_OPTION_NAME, true);
    }
}
