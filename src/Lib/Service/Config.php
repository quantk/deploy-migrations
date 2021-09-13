<?php


namespace Quantick\DeployMigration\Lib\Service;


class Config
{
    /**
     * @return string
     * @psalm-suppress UndefinedFunction
     */
    public function getMigrationsPath(): string
    {
        /** @var string $path */
        $path = config('deploy-migration.migration_path');
        return $path;
    }
}
