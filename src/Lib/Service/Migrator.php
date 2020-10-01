<?php


namespace Quantick\DeployMigration\Lib\Service;

use Carbon\Carbon;
use Illuminate\Console\OutputStyle;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Quantick\DeployMigration\Lib\DeployMigration;

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
     * @var Kernel
     */
    private $kernel;


    /**
     * Migrator constructor.
     * @param Connection $connection
     * @param Container $container
     * @param Kernel $kernel
     */
    public function __construct(
        Connection $connection,
        Container $container,
        Kernel $kernel
    )
    {
        $this->connection = $connection;
        $this->container  = $container;
        $this->kernel     = $kernel;
    }

    /**
     * @param Collection $migrations
     * @param OutputStyle $deployCommandOutput
     * @throws \Throwable
     * @psalm-suppress TooManyTemplateParams
     */
    public function run(Collection $migrations, OutputStyle $deployCommandOutput): void
    {
        /** @var DeployMigration $migration */
        foreach ($migrations as $migration) {
            $outputs = [];
            $currentMigration = $migration;
            $currentCommand = null;

            try {
                $this->connection->beginTransaction();

                $tableQuery = $this->getTableQuery();

                $alreadyExecuted = $tableQuery->where('migration', '=', get_class($migration))->count() > 0;

                if ($alreadyExecuted === true) {
                    continue;
                }

                $commands = $migration->getCommands();

                /**
                 * @var array|callable $arguments
                 */
                foreach ($commands as $commandName => $arguments) {
                    $currentCommand        = $arguments instanceof \Closure ? 'Closure#' . $commandName : $commandName;
                    $output                = $this->handleCommand($commandName, $arguments);
                    $outputs[$commandName] = $output;
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
                ]);

                $this->connection->commit();
            } catch (\Throwable $e) {
                /** @psalm-suppress DocblockTypeContradiction */
                $migrationClass = $currentMigration !== null ? get_class($currentMigration) : '';
                /** @var string $commandClass */
                $commandClass = $currentCommand;

                $deployCommandOutput->writeln('');
                $deployCommandOutput->writeln(sprintf('<error>Error during %s migration; %s command</error>', $migrationClass, $commandClass));
                $deployCommandOutput->writeln(sprintf('<error>%s</error>', (string)$e));
                $this->connection->rollBack();

                $this->getInfoTableQuery()->insert([
                    'migration' => $migrationClass,
                    'output' => json_encode($outputs),
                    'error' => json_encode([
                        'trace'         => $e->getTraceAsString(),
                        'message'       => $e->getMessage(),
                        'code'          => $e->getCode(),
                        'file'          => $e->getFile(),
                        'line'          => $e->getLine(),
                        'error_command' => $commandClass
                    ]),
                    'created_at' => Carbon::now()
                ]);

                throw $e;
            }

        }
    }

    private function handleClosure(callable $closure): string
    {
        /** @var string $output */
        $output = $this->container->call($closure);
        return $output;
    }

    /**
     * @param string $commandName
     * @param array $arguments
     * @return string
     * @throws \ReflectionException
     */
    private function handleLaravelCommand(string $commandName, array $arguments = []): string
    {
        /** @var string $signature */
        $signature = $this->getSignature($commandName);

        $this->kernel->call($signature, $arguments);
        return $this->kernel->output();
    }

    /**
     * @param string|int $commandName
     * @param array|callable $arguments
     * @return string
     * @throws \ReflectionException
     */
    private function handleCommand($commandName, $arguments): string
    {
        switch (true) {
            case $arguments instanceof \Closure:
                return $this->handleClosure($arguments);
            default:
                /**
                 * @var string $commandName
                 * @var array $arguments
                 */
                return $this->handleLaravelCommand($commandName, $arguments);
        }
    }

    /**
     * @param string $className
     * @return string|null
     * @throws \ReflectionException
     */
    private function getSignature(string $className): ?string
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        $migrationReflection = new \ReflectionClass($className);
        $properties          = $migrationReflection->getDefaultProperties();

        /** @var string|null $result */
        $result = $properties['signature'] ?? $properties['name'] ?? null;
        return $result;
    }

    /**
     * @return Builder
     */
    private function getTableQuery(): Builder
    {
        return $this->connection->table('deploy_migrations');
    }


    /**
     * @return Builder
     */
    private function getInfoTableQuery(): Builder
    {
        return $this->connection->table('deploy_migrations_info');
    }
}
