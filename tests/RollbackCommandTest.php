<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery as m;

class RollbackCommandTest extends TestCase
{
    protected function mockCommand($database, $files)
    {
        return m::mock('Arrilot\BitrixMigrations\Commands\RollbackCommand[abort, info, message, getMigrationObjectByFileName]', [$this->getConfig(), $database, $files])
            ->shouldAllowMockingProtectedMethods();
    }

    public function testItRollbacksNothingIfThereIsNoMigrations()
    {
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface');
        $database->shouldReceive('getRanMigrations')->once()->andReturn([]);

        $files = m::mock('Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface');

        $command = $this->mockCommand($database, $files);
        $command->shouldReceive('info')->with('Nothing to rollback')->once();
        $command->shouldReceive('rollBackMigration')->never();

        $this->runCommand($command);
    }

    public function testItRollbacksTheLastMigration()
    {
        // mocking friends
        $database = m::mock('Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface');
        $database->shouldReceive('getRanMigrations')->once()->andReturn([
            '2014_11_26_162220_foo',
            '2015_11_26_162220_bar',
        ]);

        $files = m::mock('Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface');

        // running the rollback
        $command = $this->mockCommand($database, $files);
        $files->shouldReceive('requireFile')->once();
        $command->shouldReceive('getMigrationObjectByFileName')->with('2014_11_26_162220_foo')->never();

        $migration = m::mock('Arrilot\BitrixMigrations\Interfaces\MigrationInterface');
        $command->shouldReceive('getMigrationObjectByFileName')->with('2015_11_26_162220_bar')->once()->andReturn($migration);
        $migration->shouldReceive('down')->once()->andReturn(true);
        $database->shouldReceive('removeSuccessfulMigrationFromLog')->with('2015_11_26_162220_bar')->once();
        $command->shouldReceive('message')->with('<info>Rolled back:</info> 2015_11_26_162220_bar.php')->once();

        $this->runCommand($command);
    }
}
