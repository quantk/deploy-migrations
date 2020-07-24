<?php


class Version20190211093348 extends \Quantick\DeployMigration\Lib\DeployMigration
{

    public function getCommands(): array
    {
        return [
            \Quantick\DeployMigration\Tests\Lib\Service\stub\TestCommand::class => [],
            function (\Illuminate\Contracts\Container\Container $container) {
                return get_class($container);
            }
        ];
    }
}
