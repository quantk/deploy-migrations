<?php


namespace Quantick\DeployMigration;


use Illuminate\Support\ServiceProvider;
use Quantick\DeployMigration\Commands\CreateMigrationCommand;
use Quantick\DeployMigration\Commands\RunDeployMigrationsCommand;

class DeployMigrationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RunDeployMigrationsCommand::class,
                CreateMigrationCommand::class
            ]);
        }

        $this->publishes([
            __DIR__ . '/Config' => config_path()
        ]);
    }

    /**
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/deploy-migration.php', 'deploy-migration');
    }
}