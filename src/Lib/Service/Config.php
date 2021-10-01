<?php


namespace Quantick\DeployMigration\Lib\Service;


class Config
{
    /**
     * @return string
     * @psalm-suppress UndefinedFunction
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     * @noinspection PhpUndefinedFunctionInspection
     */
    public function getMigrationsPath(): string
    {
        return config('deploy-migration.migration_path');
    }
}
