# Laravel package for execute one-time commands in deploy time
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/drumser/deploy-migrations/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/drumser/deploy-migrations/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/drumser/deploy-migrations/badges/build.png?b=master)](https://scrutinizer-ci.com/g/drumser/deploy-migrations/build-status/master)
[![codecov](https://codecov.io/gh/drumser/deploy-migrations/branch/master/graph/badge.svg)](https://codecov.io/gh/drumser/deploy-migrations)

## Installation
You can install the package via composer:

```
composer require quantick/deploy-migrations
```

The package will automatically register itself.

You can publish config and migration with:

```
php artisan vendor:publish --provider="Quantick\DeployMigration\DeployMigrationServiceProvider"
```

In config/deploy-migration.php сonfigure the path to the directory with migrations.
By default it will be deploy/migrations

## How to use
1. Generate migration via command:
```
php artisan make:deploy-migration
```
It will create migration class in your project.

2. Next configure getCommands method:
```php
<?php


class Version20190211093348 extends \Quantick\DeployMigration\Lib\DeployMigration {

    public function getCommands(): array
    {
        return [
            \App\Console\Commands\TestCommandWithArguments::class => ['arg' => 'value', '--option' => true],
            \App\Console\Commands\TestCommandWithoutArguments::class => [],
        ];
    }
}
```

3. Run
```
php artisan deploy:migrate
```