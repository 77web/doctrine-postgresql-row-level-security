<?php

declare(strict_types=1);

namespace Linkage\DoctrineRowLevelSecurity\Tests\Functional;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Linkage\DoctrineRowLevelSecurity\Tests\Entity\Dog;
use Linkage\DoctrineRowLevelSecurity\Tests\Entity\DogOwner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

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
        $conn = DriverManager::getConnection($connectionParams);
        foreach (explode(';', (string) file_get_contents(__DIR__ . '/drop_table.sql')) as $dropSql) {
            if (trim($dropSql) === '') {
                continue;
            }
            $conn->executeQuery($dropSql);
        }
        foreach (explode(';', (string) file_get_contents(__DIR__ . '/create_table.sql')) as $createSql) {
            if (trim($createSql) === '') {
                continue;
            }
            $conn->executeQuery($createSql);
        }

        $configuration = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__."/../Entity"],
            isDevMode: true,
            cache: new ArrayAdapter(),
        );
        $this->em = new EntityManager(
            $conn,
            $configuration,
            new EventManager(),
        );
    }

    public function testCreateSchema(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $classMetadataFactory = new ClassMetadataFactory();
        $classMetadataFactory->setEntityManager($this->em);
        $sql = $schemaTool->getCreateSchemaSql([
            $classMetadataFactory->getMetadataFor(DogOwner::class),
            $classMetadataFactory->getMetadataFor(Dog::class),
        ]);
        var_dump($sql);
    }
}
