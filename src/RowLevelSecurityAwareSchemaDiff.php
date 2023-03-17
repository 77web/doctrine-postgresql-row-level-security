<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\SchemaDiff;

class RowLevelSecurityAwareSchemaDiff
{
    public function __construct(
        private readonly SchemaDiff $delegate,
    ) {
    }

    public function toSql(AbstractPlatform $platform): array
    {
        $rlsSqls = [];
        foreach ($this->delegate->changedTables as $tableDiff) {
            if (!$tableDiff instanceof RowLevelSecurityAwareTableDiff) {
                continue;
            }

            $rlsSqls = array_merge($rlsSqls, $tableDiff->getRowLevelSecuritySqls());
        }

        return array_merge($this->delegate->toSql($platform), $rlsSqls);
    }
}
