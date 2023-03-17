<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RowLevelSecurity
{
    public function __construct(
        public readonly string $name = '',
        public readonly string $role = '',
        public readonly string $using = '',
    ) {
    }
}
