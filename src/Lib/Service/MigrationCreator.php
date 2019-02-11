<?php


namespace Quantick\DeployMigration\Lib\Service;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var Config
     */
    private $config;

    /**
     * MigrationCreator constructor.
     * @param Filesystem $filesystem
     * @param Config $config
     */
    public function __construct(
        Filesystem $filesystem,
        Config $config
    )
    {
        $this->filesystem = $filesystem;
        $this->config     = $config;
    }


    /**
     * return version path
     * @return string
     * @throws FileNotFoundException
     */
    public function create(): string
    {
        $migrationTemplate = $this->filesystem->get(__DIR__ . '/stub/DeployMigration.stub');
        $version           = $this->getVersionNumber();
        $migrationContent  = $this->prepareTemplate($migrationTemplate, [
            '{version}' => $version
        ]);
        $versionFile       = sprintf('Version%s.php', $version);
        $versionPath       = sprintf('%s/%s', $this->config->getMigrationsPath(), $versionFile);

        $this->filesystem->put($versionPath, $migrationContent);

        return $versionPath;
    }

    /**
     * @return string
     */
    private function getVersionNumber(): string
    {
        return date('Ymdhis');
    }

    private function prepareTemplate(string $templateContent, array $replaces)
    {
        return str_replace(array_keys($replaces), array_values($replaces), $templateContent);
    }
}