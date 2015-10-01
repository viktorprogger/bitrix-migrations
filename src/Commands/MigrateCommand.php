<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Repositories\DatabaseRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
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
     * Table in DB to store migrations that have been already run.
     *
     * @var string
     */
    protected $table;

    /**
     * Constructor.
     *
     * @param DatabaseRepositoryInterface $database
     * @param array $config
     */
    public function __construct(DatabaseRepositoryInterface $database, $config)
    {
        $this->database = $database;
        $this->dir = $config['dir'];
        $this->table = $config['table'];

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('migrate')->setDescription('Run all outstanding migrations');
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

    }
}