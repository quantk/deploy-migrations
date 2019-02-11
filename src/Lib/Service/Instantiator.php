<?php


namespace Quantick\DeployMigration\Lib\Service;


use Quantick\DeployMigration\Lib\DeployMigration;
use Symfony\Component\Finder\SplFileInfo;

class Instantiator
{
    /**
     * @var array
     */
    private $migrationFiles;

    /**
     * MigrationInstantiator constructor.
     * @param array|SplFileInfo[] $migrationFiles
     */
    private function __construct(array $migrationFiles)
    {
        $this->migrationFiles = $migrationFiles;
    }

    public static function create(array $migrationFiles = [])
    {
        return new static($migrationFiles);
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

            $migrationCollection->add($migration);
        }

        return $migrationCollection;
    }

    /**
     * @param SplFileInfo $fileInfo
     */
    private function requireMigration(SplFileInfo $fileInfo): void
    {
        require_once $fileInfo->getPathname();
    }

    /**
     * @param string $migrationClass
     * @return DeployMigration
     * @throws \ReflectionException
     */
    private function instantiateMigration(string $migrationClass)
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
    private function parseClassName(SplFileInfo $fileInfo)
    {
        return basename($fileInfo->getPathname(), '.php');
    }

}