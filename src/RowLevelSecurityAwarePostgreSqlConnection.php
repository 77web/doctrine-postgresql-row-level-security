<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Connection;

class RowLevelSecurityAwarePostgreSqlConnection extends Connection
{
    public function getSchemaManager()
    {
        return new RowLevelSecurityAwarePostgreSqlSchemaManager($this);
    }
}
