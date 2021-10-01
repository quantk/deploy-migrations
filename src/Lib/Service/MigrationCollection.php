<?php


namespace Quantick\DeployMigration\Lib\Service;


use Illuminate\Support\Collection;
use Quantick\DeployMigration\Lib\DeployMigration;

final class MigrationCollection extends Collection
{
    public static function create(): MigrationCollection
    {
        return new MigrationCollection();
    }

    public function addMigration(DeployMigration $migration): void
    {
        $this->push($migration);
    }
}
