<?php

namespace Arrilot\BitrixMigrations\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    /**
     * Directory where migration files are stored.
     *
     * @var string
     */
    protected $migrationDir;

    /**
     * Constructor.
     *
     * @param string $migrationDir
     */
    public function __construct($migrationDir)
    {
        $this->migrationDir = $migrationDir;

        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('make')->setDescription('Create a new migration file');
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
        $output->writeln($this->migrationDir);
    }
}