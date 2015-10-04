<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery as m;

class InstallCommandTest extends TestCase
{
    protected function mockCommand($database)
    {
        return m::mock('Arrilot\BitrixMigrations\Commands\InstallCommand[abort]', [$this->getConfig(), $database])
            ->shouldAllowMockingProtectedMethods();
    }

    public function testItCreatesMigrationTable()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface');
        $database->shouldReceive('checkMigrationTableExistence')->once()->andReturn(false);
        $database->shouldReceive('createMigrationTable')->once();

        $command = $this->mockCommand($database);

        $this->runCommand($command);
    }

    public function testItDoesNotCreateATableIfItExists()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface');
        $database->shouldReceive('checkMigrationTableExistence')->once()->andReturn(true);
        $database->shouldReceive('createMigrationTable')->never();

        $command = $this->mockCommand($database);
        $command->shouldReceive('abort')->once()->andThrow('DomainException');

        $this->runCommand($command);
    }
}
