<?php


namespace Quantick\DeployMigration\Tests\Lib\Service;


use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Quantick\DeployMigration\Lib\Service\Instantiator;
use Quantick\DeployMigration\Lib\Service\MigrationCollection;

class InstantiatorTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testRun()
    {
        $filesystem = new Filesystem();
        $migrations = $filesystem->files(__DIR__ . '/stub/migrations');

        $instantiator = Instantiator::create($migrations);

        $collection = $instantiator->run();
        static::assertTrue($collection instanceof MigrationCollection);
        static::assertFalse($collection->isEmpty());
        static::assertTrue(count($collection) === 1);
        static::assertTrue($collection->first() instanceof \Version20190211093348);
    }
}
