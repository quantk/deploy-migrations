<?php


namespace Drumser\DeployMigration\Lib;


abstract class DeployMigration
{
    abstract public function getCommands(): array;
}