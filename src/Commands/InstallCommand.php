<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface;

class InstallCommand extends AbstractCommand
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
     * @param array $config
     * @param DatabaseRepositoryInterface $database
     */
    public function __construct($config, DatabaseRepositoryInterface $database)
    {
        $this->table = $config['table'];
        $this->database = $database;

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
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        if ($this->database->checkMigrationTableExistence()) {
            $this->abort("Table \"{$this->table}\" already exists");
        }

        $this->database->createMigrationTable();

        $this->info("Migration table has been successfully created!");
    }
}