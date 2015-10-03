<?php

namespace Arrilot\BitrixMigrations\Commands;

class MakeCommand extends AbstractCommand
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
        $this->dir = $config['dir'];

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
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
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