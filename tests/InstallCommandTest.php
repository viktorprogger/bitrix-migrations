<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery as m;

class InstallCommandTest extends CommandTestCase
{
    protected function mockCommand($database)
    {
        return m::mock('Arrilot\BitrixMigrations\Commands\InstallCommand[abort]', [$database])
            ->shouldAllowMockingProtectedMethods();
    }

    public function testItCreatesMigrationTable()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseStorageInterface');
        $database->shouldReceive('checkMigrationTableExistence')->once()->andReturn(false);
        $database->shouldReceive('getTableName')->once()->andReturn('migrations');
        $database->shouldReceive('createMigrationTable')->once();

        $command = $this->mockCommand($database);

        $this->runCommand($command);
    }

    public function testItDoesNotCreateATableIfItExists()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseStorageInterface');
        $database->shouldReceive('checkMigrationTableExistence')->once()->andReturn(true);
        $database->shouldReceive('getTableName')->once()->andReturn('migrations');
        $database->shouldReceive('createMigrationTable')->never();

        $command = $this->mockCommand($database);
        $command->shouldReceive('abort')->once()->andThrow('DomainException');

        $this->runCommand($command);
    }
}
