<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;

class RowLevelSecurityAwareComparator extends Comparator
{
    public function __construct(AbstractPlatform $platform)
    {
        parent::__construct($platform);
    }

    public function compareSchemas(Schema $fromSchema, Schema $toSchema): RowLevelSecurityAwareSchemaDiff|SchemaDiff
    {
        $baseDiff = parent::compareSchemas($fromSchema, $toSchema);

        $hasRlsDiff = false;
        foreach ($toSchema->getTables() as $toTable) {
            try {
                $fromTable = $fromSchema->getTable($toTable->getName());
            } catch (SchemaException) {
                // 新規テーブルは差分がある扱いなのでcreateのイベントリスナに任せる
                continue;
            }
            $tableDiff = $this->compareTable($fromTable, $toTable);
            if ($tableDiff instanceof RowLevelSecurityAwareTableDiff) {
                $baseDiff->changedTables[$tableDiff->getOldTable()] = $tableDiff;
                $hasRlsDiff = true;
            }
        }

        return $hasRlsDiff ? new RowLevelSecurityAwareSchemaDiff($baseDiff) : $baseDiff;
    }

    public function compareTable(Table $fromTable, Table $toTable): TableDiff
    {
        $baseTableDiff = parent::compareTable($fromTable, $toTable);

        if ($toTable->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME) && !$fromTable->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME)) {
            // RLSがなかったテーブルにRLSを足した時
            $rlsTableDiff = new RowLevelSecurityAwareTableDiff($baseTableDiff);
            $rlsTableDiff->addedRowLevelSecurity = $toTable->getOption(RowLevelSecurityConfig::RLS_OPTION_NAME);

            return $rlsTableDiff;
        }
        if (!$toTable->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME) && $fromTable->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME)) {
            // RLSがあったテーブルのRLSを消した時
            $rlsTableDiff = new RowLevelSecurityAwareTableDiff($baseTableDiff);
            $rlsTableDiff->removedRowLevelSecurity = $fromTable->getOption(RowLevelSecurityConfig::RLS_OPTION_NAME);

            return $rlsTableDiff;
        }

        return $baseTableDiff;
    }
}
