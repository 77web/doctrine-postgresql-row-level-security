<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\PostgreSqlSchemaManager;
use Doctrine\DBAL\Schema\Table;

class RowLevelSecurityAwarePostgreSqlSchemaManager extends PostgreSqlSchemaManager
{
    public function __construct(
        Connection $conn,
        AbstractPlatform $platform,
    ) {
        parent::__construct($conn, $platform);
    }

    public function listTableDetails($name): Table
    {
        $table = parent::listTableDetails($name);

        $platform = $this->_platform;
        assert($platform instanceof PostgreSqlPlatform);
        $sql = <<< EOQ
SELECT
    pg_policy.polname,
    pg_authid.rolname
FROM pg_policy
    INNER JOIN pg_class ON pg_class.oid = pg_policy.polrelid
    INNER JOIN pg_authid ON pg_authid.oid = any(pg_policy.polroles)
WHERE
    pg_class.relname = ?
    AND pg_class.relkind = 'r'
    AND pg_class.relrowsecurity = true
EOQ;

        try {
            $rlsOptions = $this->_conn->fetchAssociative($sql, [$name]);

            if ($rlsOptions !== false) {
                $table->addOption('rowLevelSecurity', [
                    'name' => $rlsOptions['polname'],
                    'role' => $rlsOptions['rolname'],
                ]);
            }
        } catch (DriverException) {
            // 何もしない
        }

        return $table;
    }

    public function createComparator(): Comparator
    {
        return new RowLevelSecurityAwareComparator($this->_platform);
    }
}
