<?php


namespace Quantick\DeployMigration\Tests\Lib\Console;


use PHPUnit\Framework\TestCase;
use Quantick\DeployMigration\Lib\Console\Output;

class OutputTest extends TestCase
{
    public function testGetMessages()
    {
        $output = new Output();

        $output->write('message1');
        $output->write('message2');

        $messages = $output->getMessages();
        static::assertTrue(count($messages) === 2);
        static::assertTrue($messages[0] === 'message1');
        static::assertTrue($messages[1] === 'message2');
    }
}
