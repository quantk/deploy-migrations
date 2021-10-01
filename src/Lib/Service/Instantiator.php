<?php


namespace Quantick\DeployMigration\Lib\Service;


use Quantick\DeployMigration\Lib\DeployMigration;
use Symfony\Component\Finder\SplFileInfo;

final class Instantiator
{
    /**
     * @var array
     * @psalm-var array<SplFileInfo>
     */
    private $migrationFiles;

    /**
     * MigrationInstantiator constructor.
     * @param array|SplFileInfo[] $migrationFiles
     */
    private function __construct(array $migrationFiles)
    {
        /** @var array<SplFileInfo> $migrationFiles */
        $this->migrationFiles = $migrationFiles;
    }

    public static function create(array $migrationFiles = []): Instantiator
    {
        return new Instantiator($migrationFiles);
    }

    /**
     * @throws \ReflectionException
     */
    public function run(): MigrationCollection
    {
        $migrationCollection = MigrationCollection::create();

        foreach ($this->migrationFiles as $migrationFile) {
            $this->requireMigration($migrationFile);
            $migration = $this->instantiateMigration($this->parseClassName($migrationFile));

            $migrationCollection->addMigration($migration);
        }

        return $migrationCollection;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @psalm-suppress UnresolvableInclude
     */
    private function requireMigration(SplFileInfo $fileInfo): void
    {
        require_once $fileInfo->getPathname();
    }

    /**
     * @param string $migrationClass
     * @return DeployMigration
     * @throws \ReflectionException
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function instantiateMigration(string $migrationClass): DeployMigration
    {
        $migrationReflection = new \ReflectionClass($migrationClass);
        /** @var DeployMigration $instance */
        $instance = $migrationReflection->newInstance();
        return $instance;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return string
     */
    private function parseClassName(SplFileInfo $fileInfo): string
    {
        return basename($fileInfo->getPathname(), '.php');
    }

}
