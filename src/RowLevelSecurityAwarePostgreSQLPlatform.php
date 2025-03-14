<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Table;

class RowLevelSecurityAwarePostgreSQLPlatform extends PostgreSQLPlatform
{
    public function __construct(
        private readonly RowLevelSecuritySqlFactory $rowLevelSecuritySqlFactory
    ) {
    }

    public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES)
    {
        $sqls = parent::getCreateTableSQL($table, $createFlags);
        if ($table->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME)) {
            $sqls = array_merge($sqls, $this->createRowLevelSecuritySqlsForTable($table));
        }

        return $sqls;
    }

    /**
     * @param list<Table> $tables
     * @return list<string>
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCreateTablesSQL(array $tables): array
    {
        $sqls = parent::getCreateTablesSQL($tables);
        foreach ($tables as $table) {
            if ($table->hasOption(RowLevelSecurityConfig::RLS_OPTION_NAME)) {
                $sqls = array_merge($sqls, $this->createRowLevelSecuritySqlsForTable($table));
            }
        }

        return $sqls;
    }

    /**
     * @param Table $table
     * @return list<string>
     */
    private function createRowLevelSecuritySqlsForTable(Table $table): array
    {
        /** @var array{name: string, role: string, using: string} $rlsOptions */
        $rlsOptions = $table->getOption(RowLevelSecurityConfig::RLS_OPTION_NAME);
        return $this->rowLevelSecuritySqlFactory->createEnableSqls(
            $rlsOptions['name'],
            $table->getName(),
            $rlsOptions['role'],
            $rlsOptions['using'],
        );
    }

}
