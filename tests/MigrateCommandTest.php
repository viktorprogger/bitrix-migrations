<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery as m;

class MigrateCommandTest extends TestCase
{
    protected function mockCommand($database, $files)
    {
        return m::mock('Arrilot\BitrixMigrations\Commands\MigrateCommand[abort, info, message, getMigrationObjectByFileName]', [$this->getConfig(), $database, $files])
            ->shouldAllowMockingProtectedMethods();
    }

    public function testItMigratesNothingIfThereIsNoOutstandingMigrations()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface');
        $database->shouldReceive('getRanMigrations')->once()->andReturn([
            '2014_11_26_162220_foo',
            '2015_11_26_162220_bar',
        ]);

        $files = m::mock('Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface');
        $files->shouldReceive('getMigrationFiles')->once()->andReturn([
            '2015_11_26_162220_bar',
            '2014_11_26_162220_foo',
        ]);

        $command = $this->mockCommand($database, $files);
        $command->shouldReceive('info')->with('Nothing to migrate')->once();
        $command->shouldReceive('runMigration')->never();

        $this->runCommand($command);
    }

    public function testItMigratesOutstandingMigrations()
    {
        // mocking friends
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface');
        $database->shouldReceive('getRanMigrations')->once()->andReturn([
            '2014_11_26_162220_foo',
        ]);

        $files = m::mock('Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface');
        $files->shouldReceive('getMigrationFiles')->once()->andReturn([
            '2015_11_26_162220_bar',
            '2014_11_26_162220_foo',
        ]);

        // running the migration
        $command = $this->mockCommand($database, $files);
        $files->shouldReceive('requireFile')->once();
        $command->shouldReceive('getMigrationObjectByFileName')->with('2014_11_26_162220_foo')->never();

        $migration = m::mock('Arrilot\BitrixMigrations\Interfaces\MigrationInterface');
        $command->shouldReceive('getMigrationObjectByFileName')->with('2015_11_26_162220_bar')->once()->andReturn($migration);
        $migration->shouldReceive('up')->once()->andReturn(true);
        $database->shouldReceive('logSuccessfulMigration')->with('2015_11_26_162220_bar')->once();
        $command->shouldReceive('message')->with('<info>Migrated:</info> 2015_11_26_162220_bar.php')->once();

        $this->runCommand($command);
    }
}
