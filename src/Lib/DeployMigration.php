<?php


namespace Quantick\DeployMigration\Lib;


abstract class DeployMigration
{
    abstract public function getCommands(): array;
}