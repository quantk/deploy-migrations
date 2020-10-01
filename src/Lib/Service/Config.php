<?php


namespace Quantick\DeployMigration\Lib\Service;


use Illuminate\Contracts\Container\BindingResolutionException;

class Config
{
    /**
     * @return string
     * @throws BindingResolutionException
     */
    public function getMigrationsPath(): string
    {
        /** @var string $path */
        $path = config('deploy-migration.migration_path');
        return $path;
    }
}
