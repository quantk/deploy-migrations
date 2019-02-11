<?php


namespace Quantick\DeployMigration\Commands;


use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Illuminate\Filesystem\Filesystem;
use Quantick\DeployMigration\Lib\DeployMigration;
use Quantick\DeployMigration\Lib\Service\Instantiator;
use Quantick\DeployMigration\Lib\Service\Migrator;

class RunDeployMigrationsCommand extends BaseDeployMigrationCommand
{
    protected $signature = 'deploy:migrate';

    protected $description = 'Run migrate commands in deploy time';
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var Builder
     */
    private $schemaBuilder;
    /**
     * @var Migrator
     */
    private $migrator;

    /**
     * DeployMigrateCommand constructor.
     * @param Connection $connection
     * @param Filesystem $filesystem
     * @param Migrator $migrator
     */
    public function __construct(
        Connection $connection,
        Filesystem $filesystem,
        Migrator $migrator
    )
    {
        parent::__construct();
        $this->connection    = $connection;
        $this->schemaBuilder = $connection->getSchemaBuilder();
        $this->filesystem    = $filesystem;
        $this->migrator      = $migrator;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     * @throws \Throwable
     */
    public function handle()
    {
        $this->prepareConsoleStyles();
        if ($this->hasMigrationTable() === false) {
            throw new \RuntimeException('Table `deploy_migrations` not found. You probably should execute migrationFiles');
        }

        $migrationsPath = $this->getMigrationsPath();
        $migrationFiles = $this->filesystem->files($migrationsPath);

        $instantiator = Instantiator::create($migrationFiles);

        $migrationCollection = $instantiator->run();

        $migrations = $migrationCollection->filter(function (DeployMigration $migration) {
            $tableQuery      = $this->getTableQuery();
            $alreadyExecuted = $tableQuery->where('migration', '=', get_class($migration))->count() > 0;
            return $alreadyExecuted === false;
        });

        $migrationsCount = count($migrations);
        if ($migrationsCount === 0) {
            $this->output->writeln('<info>Nothing to migrate...</info>');
            return;
        }

        $this->output->progressStart(count($migrations));

        try {
            $this->migrator->run($migrations, $this->output);
        } catch (\Throwable $e) {
            throw $e;
        }

        $this->output->progressFinish();
        $this->output->writeln('<info>Deploy migrations finished</info>');
    }

    private function hasMigrationTable(): bool
    {
        return $this->schemaBuilder->hasTable('deploy_migrations');
    }

    /**
     * @return string
     */
    private function getMigrationsPath(): string
    {
        return config('deploy-migration.migration_path');
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private function getTableQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->connection->table('deploy_migrations');
    }
}