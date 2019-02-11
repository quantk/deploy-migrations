<?php


namespace Drumser\DeployMigration\Lib\Service;


class MigrationCreator
{
    /**
     * return version path
     * @return string
     */
    public function create(): string
    {
        $migrationTemplate = file_get_contents(__DIR__ . '/stub/DeployMigration.stub');
        $version           = $this->getVersionNumber();
        $migrationContent  = $this->prepareTemplate($migrationTemplate, [
            '{version}' => $version
        ]);
        $versionFile       = sprintf('Version%s.php', $version);
        $versionPath       = sprintf('%s/%s', config('deploy-migration.migration_path'), $versionFile);
        file_put_contents(
            $versionPath,
            $migrationContent
        );

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