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
    protected $dir;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->dir = $_SERVER['DOCUMENT_ROOT'].'/'.$config['dir'];

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
        $this->ensureDirExists();
    }

    /**
     * Create a migration directory if it does not exist.
     */
    protected function ensureDirExists()
    {
        if (!file_exists($this->dir)) {
            mkdir($this->dir, 0755, true);
        }
    }
}