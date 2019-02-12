<?php


namespace Quantick\DeployMigration\Tests\Lib\Service;


use Illuminate\Console\OutputStyle;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Quantick\DeployMigration\Lib\Service\Instantiator;
use Quantick\DeployMigration\Lib\Service\Migrator;
use Quantick\DeployMigration\Tests\Lib\Service\stub\TestCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class MigratorTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function testRun()
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(static::once())->method('beginTransaction');
        $connection->expects(static::once())->method('commit');
        $container = $this->createMock(Container::class);

        $instantiator = Instantiator::create((new Filesystem())->files(__DIR__ . '/stub/migrations'));
        $collection   = $instantiator->run();

        $migrator = new Migrator($connection, $container);

        $builder     = $this->createMock(Builder::class);
        $infoBuilder = $this->createMock(Builder::class);

        $connection->expects(static::exactly(2))->method('table')
                   ->willReturnOnConsecutiveCalls(
                       $builder,
                       $infoBuilder
                   )
        ;

        $builder->expects(static::once())->method('insert');
        $infoBuilder->expects(static::once())->method('insert');

        $builder->method('where')->willReturn($builder);
        $builder->method('count')->willReturn(0);

        $testCommand = new TestCommand();

        $container->method('get')->with(TestCommand::class)->willReturn($testCommand);


        $output = new OutputStyle(new ArrayInput([]), new ConsoleOutput());
        $output->progressStart(1);

        $container->method('make')->willReturn($output);
        $migrator->run($collection, $output);
        $output->progressFinish();
    }

    public function testRunWithError()
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(static::once())->method('beginTransaction');
        $connection->expects(static::once())->method('rollBack');
        $container = $this->createMock(Container::class);

        $instantiator = Instantiator::create((new Filesystem())->files(__DIR__ . '/stub/migrations'));
        $collection   = $instantiator->run();

        $migrator = new Migrator($connection, $container);

        $builder     = $this->createMock(Builder::class);
        $infoBuilder = $this->createMock(Builder::class);

        $connection->expects(static::exactly(2))->method('table')
                   ->willReturnOnConsecutiveCalls(
                       $builder,
                       $infoBuilder
                   )
        ;

        $infoBuilder->expects(static::once())->method('insert');

        $builder->method('where')->willReturn($builder);
        $builder->method('count')->willReturn(0);

        $testCommand = $this->createMock(TestCommand::class);

        $container->method('get')->with(TestCommand::class)->willReturn($testCommand);

        $output = new OutputStyle(new ArrayInput([]), new ConsoleOutput());
        $testCommand->method('run')->willThrowException(new \RuntimeException('exception'));
        $output->progressStart(1);

        $container->method('make')->willReturn($output);
        $this->expectException(\RuntimeException::class);
        $migrator->run($collection, $output);
        $output->progressFinish();
    }
}