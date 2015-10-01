<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Repositories\DatabaseRepositoryInterface;
use Illuminate\Support\Str;
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
     * Constructor.
     *
     * @param DatabaseRepositoryInterface $database
     * @param array $config
     */
    public function __construct(DatabaseRepositoryInterface $database, $config)
    {
        $this->database = $database;
        $this->dir = $_SERVER['DOCUMENT_ROOT'].'/'.$config['dir'];

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
        $migrations = $this->getMigrationsToRun();

        if (!$migrations) {
            return $output->writeln("<info>Nothing to migrate</info>");
        }

        foreach ($migrations as $migration) {
            $this->runMigration($migration);
        }

        echo "<pre>"; var_dump($migrations); echo "</pre>";
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function determineMigrationClass($file)
    {
        $file = implode('_', array_slice(explode('_', $file), 4));

        $class = Str::studly($file);

        return new $class;
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string  $path
     * @return array
     */
    public function getMigrationFiles($path)
    {
        $files = glob($path.'/*_*.php');

        if ($files === false) {
            return [];
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));

        }, $files);

        sort($files);

        return $files;
    }

    /**
     * Get array of migrations that should be ran.
     *
     * @return array
     */
    protected function getMigrationsToRun()
    {
        $allMigrations = $this->getMigrationFiles($this->dir);

        $ranMigrations = $this->database->getRanMigrations();

        return array_diff($allMigrations, $ranMigrations);
    }

    /**
     * Run a given migration.
     *
     * @param $migration
     */
    protected function runMigration($migration)
    {
        require_once $this->dir . '/' . $migration . '.php';

        $class = $this->determineMigrationClass($migration);
    }
}