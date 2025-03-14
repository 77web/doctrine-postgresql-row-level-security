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
}
