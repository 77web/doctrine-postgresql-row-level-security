<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class RowLevelSecurityAwarePostgreSqlConnection extends Connection
{
    public function getSchemaManager(): AbstractSchemaManager
    {
        return new RowLevelSecurityAwarePostgreSqlSchemaManager($this);
    }
}
