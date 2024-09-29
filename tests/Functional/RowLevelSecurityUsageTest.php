<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class RowLevelSecurityUsageTest extends TestCase
{
    private Connection $conn;

    protected function setUp(): void
    {
        $connectionParams = [
            'dbname' => 'test',
            'user' => 'test',
            'password' => 'password',
            'host' => 'localhost',
            'driver' => 'pdo_pgsql',
        ];
        $this->conn = DriverManager::getConnection($connectionParams);
        $this->conn->executeQuery(file_get_contents(__DIR__ . '/drop_table.sql'));
        $this->conn->executeQuery(file_get_contents(__DIR__ . '/create_table.sql'));
    }
}
