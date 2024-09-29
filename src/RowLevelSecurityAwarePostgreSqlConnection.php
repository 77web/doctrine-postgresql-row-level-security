<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class RowLevelSecurityAwarePostgreSqlConnection extends Connection
{
    public function createSchemaManager(): AbstractSchemaManager
    {
        return new RowLevelSecurityAwarePostgreSqlSchemaManager($this, $this->getDatabasePlatform());
    }

    // @deprecated
    public function getSchemaManager(): AbstractSchemaManager
    {
        return $this->createSchemaManager();
    }
}
