<?php

namespace Arrilot\BitrixMigrations\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownCommand extends Command
{
    /**
     * Directory where migration files are stored.
     *
     * @var string
     */
    protected $migrationDir;

    /**
     * Table in DB to store migrations that have been already run.
     *
     * @var string
     */
    protected $migrationTable;
    /**
     * Constructor.
     *
     * @param string $migrationDir
     * @param string $migrationTable
     */
    public function __construct($migrationDir, $migrationTable)
    {
        $this->migrationDir = $migrationDir;
        $this->migrationTable = $migrationTable;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('down')->setDescription('Run migrations down');
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