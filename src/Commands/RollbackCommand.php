<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Migrator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RollbackCommand extends AbstractCommand
{
    /**
     * Migrator instance
     *
     * @var Migrator
     */
    protected $migrator;

    /**
     * Constructor.
     *
     * @param Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('rollback')
            ->setDescription('Rollback the last migration')
            ->addOption('hard', null, InputOption::VALUE_NONE, 'Rollback without running down()');
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $ran = $this->migrator->getRanMigrations();

        if (!$ran) {
            return $this->info('Nothing to rollback');
        }

        $migration = $ran[count($ran) - 1];

        return $this->input->getOption('hard')
            ? $this->hardRollbackMigration($migration)
            : $this->rollbackMigration($migration);
    }

    /**
     * Call rollback.
     *
     * @param $migration
     * @return null
     */
    protected function rollbackMigration($migration)
    {
        if ($this->migrator->doesMigrationFileExist($migration)) {
            $this->migrator->rollbackMigration($migration);
            $this->message("<info>Rolled back:</info> {$migration}.php");
        } else {
            $this->markRolledBackWithConfirmation($migration);
        }
    }

    /**
     * Call hard rollback.
     *
     * @param $migration
     * @return null
     */
    protected function hardRollbackMigration($migration)
    {
        $this->migrator->removeSuccessfulMigrationFromLog($migration);

        $this->message("<info>Rolled back with --hard:</info> {$migration}.php");
    }

    /**
     * Ask a user to confirm rolling back non-existing migration and remove it from log.
     *
     * @param $migration
     *
     * @return void
     */
    protected function markRolledBackWithConfirmation($migration)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("<error>Migration $migration was not found.\r\nDo you want to mark it as rolled back? (y/n)</error>\r\n", false);

        if (!$helper->ask($this->input, $this->output, $question)) {
            $this->abort();
        }

        $this->migrator->removeSuccessfulMigrationFromLog($migration);
    }
}
