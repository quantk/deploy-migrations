<?php


namespace Quantick\DeployMigration\Lib\Service;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Quantick\DeployMigration\Lib\Console\Output;
use Symfony\Component\Console\Input\ArrayInput;

class Migrator
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Container
     */
    private $container;


    /**
     * Migrator constructor.
     * @param Connection $connection
     * @param Container $container
     */
    public function __construct(
        Connection $connection,
        Container $container
    )
    {
        $this->connection = $connection;
        $this->container  = $container;
    }

    /**
     * @param Collection $migrations
     * @param OutputStyle $deployCommandOutput
     * @throws \Throwable
     */
    public function run(Collection $migrations, OutputStyle $deployCommandOutput)
    {
        $currentMigration = null;
        $currentCommand   = null;
        foreach ($migrations as $migration) {
            $outputs = [];

            try {
                $this->connection->beginTransaction();

                $tableQuery = $this->getTableQuery();

                $alreadyExecuted = $tableQuery->where('migration', '=', get_class($migration))->count() > 0;

                if ($alreadyExecuted === true) {
                    continue;
                }

                $currentMigration = $migration;
                $commands         = $migration->getCommands();

                foreach ($commands as $commandName => $arguments) {
                    /** @var Command $command */
                    $command        = $this->container->get($commandName);
                    $currentCommand = $command;
                    $command->setLaravel($this->container);

                    $input  = new ArrayInput($arguments);
                    $output = new Output();

                    // doing this shit for collecting output
                    try {
                        $command->run($input, $output);
                    } catch (\Throwable $e) {
                        $outputs[$commandName] = $output->getMessages();

                        throw $e;
                    }

                    $outputs[$commandName] = $output->getMessages();
                }

                $deployCommandOutput->progressAdvance();

                $tableQuery->insert([
                    'migration' => get_class($migration),
                    'created_at' => Carbon::now()

                ]);

                $this->getInfoTableQuery()->insert([
                    'migration' => get_class($migration),
                    'output' => json_encode($outputs),
                    'created_at' => Carbon::now()
                ])
                ;

                $this->connection->commit();
            } catch (\Throwable $e) {
                $deployCommandOutput->writeln('');
                $deployCommandOutput->writeln(sprintf('<error>Error during %s migration; %s command</error>', get_class($currentMigration), get_class($currentCommand)));
                $deployCommandOutput->writeln(sprintf('<error>%s</error>', (string)$e));
                $this->connection->rollBack();

                $this->getInfoTableQuery()->insert([
                    'migration' => get_class($migration),
                    'output' => json_encode($outputs),
                    'error' => json_encode([
                        'trace' => $e->getTraceAsString(),
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]),
                    'created_at' => Carbon::now()
                ])
                ;

                throw $e;
            }

        }
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private function getTableQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->connection->table('deploy_migrations');
    }


    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private function getInfoTableQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->connection->table('deploy_migrations_info');
    }
}