<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\MigrationInterface;
use Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface;
use Illuminate\Support\Str;

class RollbackCommand extends AbstractCommand
{
    /**
     * Interface that gives us access to the database.
     *
     * @var DatabaseRepositoryInterface
     */
    protected $database;

    /**
     * Directory where migration files are stored.
     *
     * @var string
     */
    protected $dir;

    /**
     * Constructor.
     *
     * @param DatabaseRepositoryInterface $database
     * @param array $config
     */
    public function __construct($config, DatabaseRepositoryInterface $database)
    {
        $this->database = $database;
        $this->dir = $config['dir'];

        parent::__construct();
    }

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

    }
}