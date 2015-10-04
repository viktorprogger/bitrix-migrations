<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;
use Arrilot\BitrixMigrations\Interfaces\MigrationInterface;
use Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface;
use Arrilot\BitrixMigrations\Repositories\FileRepository;
use Illuminate\Support\Str;

class RollbackCommand extends AbstractMigrationCommand
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
     * Files interactions.
     *
     * @var FileRepositoryInterface
     */
    protected $files;

    /**
     * Constructor.
     *
     * @param array $config
     * @param DatabaseRepositoryInterface $database
     * @param FileRepositoryInterface $files
     */
    public function __construct($config, DatabaseRepositoryInterface $database, FileRepositoryInterface $files = null)
    {
        $this->database = $database;
        $this->dir = $config['dir'];
        $this->files = $files ?: new FileRepository();

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
        $migration = end($this->database->getRanMigrations());

        if ($migration) {
            $this->rollbackMigration($migration);
        } else {
            $this->info('Nothing to rollback');
        }
    }

    /**
     * Rollback a given migration.
     *
     * @param string $file
     * @return mixed
     */
    protected function rollbackMigration($file)
    {
        $this->files->requireFile($this->dir . '/' . $file . '.php');

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
}
