<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Doctrine\DBAL\Schema\TableDiff;

class RowLevelSecurityAwareTableDiff extends TableDiff
{
    public ?array $addedRowLevelSecurity = null;
    public ?array $removedRowLevelSecurity = null;

    public function __construct(
        TableDiff $baseDiff,
    ) {
        parent::__construct(
            $baseDiff->getOldTable()->getName(),
            $baseDiff->addedColumns,
            $baseDiff->changedColumns,
            $baseDiff->removedColumns,
            $baseDiff->addedIndexes,
            $baseDiff->changedIndexes,
            $baseDiff->removedIndexes,
            $baseDiff->fromTable
        );
    }

    /**
     * @return array<string>
     */
    public function getRowLevelSecuritySqls(): array
    {
        $sqlFactory = new RowLevelSecuritySqlFactory();

        if ($this->addedRowLevelSecurity !== null) {
            $using = $this->addedRowLevelSecurity['using'] ?? '## TODO 手動設定要 ##';
            return $sqlFactory->createEnableSqls(
                $this->addedRowLevelSecurity['name'],
                $this->getOldTable()->getName(),
                $this->addedRowLevelSecurity['role'],
                $using,
            );
        }
        if ($this->removedRowLevelSecurity !== null) {
            return $sqlFactory->createDisableSqls(
                $this->removedRowLevelSecurity['name'],
                $this->getOldTable()->getName(),
            );
        }

        return [];
    }
}
