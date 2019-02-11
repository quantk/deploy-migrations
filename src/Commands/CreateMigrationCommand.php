<?php


namespace Drumser\DeployMigration\Commands;


use Drumser\DeployMigration\Lib\Service\MigrationCreator;

class CreateMigrationCommand extends BaseDeployMigrationCommand
{
    protected $signature = 'make:deploy-migration';

    protected $description = 'Create deploy migration';
    /**
     * @var MigrationCreator
     */
    private $creator;

    /**
     * DeployMigrateCommand constructor.
     * @param MigrationCreator $creator
     */
    public function __construct(MigrationCreator $creator)
    {
        parent::__construct();
        $this->creator = $creator;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $this->prepareConsoleStyles();
        $versionPath = $this->creator->create();

        $this->output->writeln(sprintf('<info>Deploy migration generated in %s</info>', $versionPath));
    }
}