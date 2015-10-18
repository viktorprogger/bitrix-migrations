<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
            $file = $ran[count($ran) - 1];
            $this->files->exists($this->getMigrationFilePath($file))
                ? $this->rollbackMigration($file)
                : $this->markRolledBackWithConfirmation($file);
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

    /**
     * Ask a user to confirm rolling back non-existing migration and remove it from log.
     *
     * @param $file
     *
     * @return void
     */
    protected function markRolledBackWithConfirmation($file)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("<error>Migration $file was not found.\r\nDo you want to mark it as rolled back? (y/n)</error>\r\n", false);

        if (!$helper->ask($this->input, $this->output, $question)) {
            $this->abort();
        }

        $this->database->removeSuccessfulMigrationFromLog($file);
    }
}
