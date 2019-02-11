<?php


namespace Quantick\DeployMigration\Tests\Lib\Service;


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Quantick\DeployMigration\Lib\Service\Config;
use Quantick\DeployMigration\Lib\Service\MigrationCreator;

class MigrationCreatorTest extends TestCase
{
    /**
     * @throws FileNotFoundException
     */
    public function testCreate()
    {
        /** @var Filesystem|MockObject $filesystem */
        $filesystem = $this->createPartialMock(Filesystem::class, ['put']);
        $filesystem->expects(static::once())->method('put')->willReturn(true);

        $config = $this->createMock(Config::class);
        $config->method('getMigrationsPath')->willReturn('path');
        $creator = new MigrationCreator($filesystem, $config);

        $version = $creator->create();
        static::assertIsString($version);
    }
}