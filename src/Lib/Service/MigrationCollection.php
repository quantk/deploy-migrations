<?php


namespace Quantick\DeployMigration\Lib\Service;


use Illuminate\Support\Collection;
use Quantick\DeployMigration\Lib\DeployMigration;

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