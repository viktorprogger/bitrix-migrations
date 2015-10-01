<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Repositories\DatabaseRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    /**
     * Interface that gives us access to the database.
     *
     * @var DatabaseRepositoryInterface
     */
    protected $database;

    /**
     * Table in DB to store migrations that have been already run.
     *
     * @var string
     */
    protected $table;

    /**
     * Constructor.
     *
     * @param DatabaseRepositoryInterface $database
     * @param string $migrationTable
     */
    public function __construct(DatabaseRepositoryInterface $database, $migrationTable)
    {
        $this->database = $database;
        $this->table = $migrationTable;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('install')->setDescription('Create the migration database table');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->database->checkTableExistence($this->table)) {
            $output->writeln("<error>Table \"{$this->table}\" already exists</error>");

            return false;
        }

        $this->database->createMigrationTable($this->table);

        return $output->writeln("<info>Migration table has been successfully created!</info>");
    }
}