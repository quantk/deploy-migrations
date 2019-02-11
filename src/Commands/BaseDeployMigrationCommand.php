<?php


namespace Drumser\DeployMigration\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class BaseDeployMigrationCommand extends Command
{
    /**
     * BaseDeployMigrationCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function prepareConsoleStyles(): void
    {
        $errorStyle = new OutputFormatterStyle('white', 'red', ['bold']);
        $this->output->getFormatter()->setStyle('error', $errorStyle);

        $infoStyle = new OutputFormatterStyle('white', 'blue', ['bold']);
        $this->output->getFormatter()->setStyle('info', $infoStyle);
    }
}