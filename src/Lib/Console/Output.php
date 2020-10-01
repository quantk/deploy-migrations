<?php


namespace Quantick\DeployMigration\Lib\Console;


class Output extends \Symfony\Component\Console\Output\Output
{
    /**
     * @var array
     * @psalm-var array<string>
     */
    private $messages = [];

    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool $newline Whether to add a newline or not
     */
    protected function doWrite($message, $newline): void
    {
        $this->messages[] = $message;
    }
}
