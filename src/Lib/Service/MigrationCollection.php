<?php


namespace Drumser\DeployMigration\Lib\Service;


use Drumser\DeployMigration\Lib\DeployMigration;
use Illuminate\Support\Collection;

class MigrationCollection extends Collection
{
    public static function create()
    {
        return new static();
    }

    public function add(DeployMigration $migration)
    {
        $this->push($migration);
    }
}