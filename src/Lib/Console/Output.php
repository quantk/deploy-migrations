<?php


namespace Drumser\DeployMigration\Lib\Console;


class Output extends \Symfony\Component\Console\Output\Output
{
    private $messages = [];

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool $newline Whether to add a newline or not
     */
    protected function doWrite($message, $newline)
    {
        $this->messages[] = $message;
    }
}