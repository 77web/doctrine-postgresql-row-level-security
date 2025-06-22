# linkage/doctrine-row-level-security

A package to provide projects with Doctrine ORM PostgreSQL's Row Level Security features.

## Installation

1. Install this package via composer.

```shell
composer require linkage/doctrine-row-level-security
```

2. Add this package's EventSubscriber to your doctrine configuration

for Symfony:

```yaml
# services.yaml
services:
    rls.subscriber:
        class: Linkage\DoctrineRowLevelSecurity\RowLevelSecurityListener
        tags:
            - { name: doctrine.event_subscriber, connection: default }
```

for laravel-doctrine/orm:

```php
// config/doctrine.php
            'events'        => [
                'listeners'   => [],
                'subscribers' => [
                    Linkage\DoctrineRowLevelSecurity\RowLevelSecurityListener::class,
                ],
            ],
```

## Usage

Add "#[RowLevelSecurity]" attribute to your Entity associated with table under Row Level Security.

There are 3 required options for RowLevelSecurity attribute:

- name: a name for policy which should be attached to the table
- role: a name of role which should be attached to RowLevelSecurity users
- using: a sql clause which describes the role's access condition

Then you can use doctrine:migrations:diff or doctrine:schema:create command as usual.
SQL to enable Row Level Security will appear in your migration files or be executed on schema creation.

