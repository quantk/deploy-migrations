<?php


namespace Quantick\DeployMigration\Lib\Service;


class Config
{
    public function getMigrationsPath()
    {
        return config('deploy-migration.migration_path');
    }
}