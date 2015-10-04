<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class RollbackCommand extends AbstractMigrationCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('rollback')->setDescription('Rollback the last migration');
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $ran = $this->database->getRanMigrations();

        if ($ran) {
            $this->rollbackMigration($ran[count($ran)-1]);
        } else {
            $this->info('Nothing to rollback');
        }
    }

    /**
     * Rollback a given migration.
     *
     * @param string $file
     *
     * @return mixed
     */
    protected function rollbackMigration($file)
    {
        $migration = $this->getMigrationObjectByFileName($file);

        try {
            if ($migration->down() === false) {
                $this->message("<error>Can't rollback migration:</error> {$file}.php");
                $this->abort();
            }
        } catch (MigrationException $e) {
            $this->abort($e->getMessage());
        }

        $this->database->removeSuccessfulMigrationFromLog($file);

        $this->message("<info>Rolled back:</info> {$file}.php");
    }
}
