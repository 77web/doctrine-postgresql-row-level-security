<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

class RowLevelSecuritySqlFactory
{
    /**
     * @return array<string>
     */
    public function createEnableSqls(string $policy, string $table, string $role, string $using): array
    {
        return [
            sprintf('CREATE POLICY %s ON %s TO %s USING (%s)', $policy, $table, $role, $using),
            sprintf('GRANT ALL ON TABLE %s TO %s', $table, $role),
            sprintf('ALTER TABLE %s ENABLE ROW LEVEL SECURITY', $table),
        ];
    }

    /**
     * @return array
     */
    public function createDisableSqls(string $policy, string $table): array
    {
        return [
            sprintf('DROP POLICY %s ON %s', $policy, $table),
            sprintf('ALTER TABLE %s DISABLE ROW LEVEL SECURITY', $table),
        ];
    }
}
