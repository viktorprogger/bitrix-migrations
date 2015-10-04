<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;
use Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface;
use Arrilot\BitrixMigrations\Repositories\FileRepository;

class MigrateCommand extends AbstractMigrationCommand
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
        $this->setName('migrate')->setDescription('Run all outstanding migrations');
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $migrations = $this->getMigrationsToRun();

        if (!empty($migrations)) {
            foreach ($migrations as $migration) {
                $this->runMigration($migration);
            }
        } else {
            $this->info('Nothing to migrate');
        }
    }

    /**
     * Get array of migrations that should be ran.
     *
     * @return array
     */
    protected function getMigrationsToRun()
    {
        $allMigrations = $this->files->getMigrationFiles($this->dir);

        $ranMigrations = $this->database->getRanMigrations();

        return array_diff($allMigrations, $ranMigrations);
    }

    /**
     * Run a given migration.
     *
     * @param string $file
     * @return mixed
     */
    protected function runMigration($file)
    {
        $this->files->requireFile($this->dir . '/' . $file . '.php');

        $migration = $this->getMigrationObjectByFileName($file);

        try {
            if ($migration->up() === false) {
                $this->abort("Migration up from {$file}.php returned false");
            }
        } catch (MigrationException $e) {
            $this->abort($e->getMessage());
        }

        $this->database->logSuccessfulMigration($file);

        $this->message("<info>Migrated:</info> {$file}.php");
    }
}
