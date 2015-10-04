<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\DatabaseRepositoryInterface;
use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;
use Arrilot\BitrixMigrations\Interfaces\MigrationInterface;
use Arrilot\BitrixMigrations\Repositories\FileRepository;
use Illuminate\Support\Str;

abstract class AbstractMigrationCommand extends AbstractCommand
{
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
     * Interface that gives us access to the database.
     *
     * @var DatabaseRepositoryInterface
     */
    protected $database;

    /**
     * Constructor.
     *
     * @param array                       $config
     * @param DatabaseRepositoryInterface $database
     * @param FileRepositoryInterface     $files
     */
    public function __construct($config, DatabaseRepositoryInterface $database, FileRepositoryInterface $files = null)
    {
        $this->database = $database;
        $this->dir = $config['dir'];
        $this->files = $files ?: new FileRepository();

        parent::__construct();
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param string $file
     *
     * @return MigrationInterface
     */
    protected function getMigrationObjectByFileName($file)
    {
        $class = $this->getMigrationClassNameByFileName($file);

        $this->requireMigrationFile($file);

        $object = new $class();

        if (!$object instanceof MigrationInterface) {
            $this->abort("Migration class {$class} must implement Arrilot\\BitrixMigrations\\Interfaces\\MigrationInterface");
        }

        return $object;
    }

    /**
     * Get a migration class name by a migration file name.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getMigrationClassNameByFileName($file)
    {
        $fileExploded = explode('_', $file);

        $datePart = implode('_', array_slice($fileExploded, 0, 4));
        $namePart = implode('_', array_slice($fileExploded, 4));

        return Str::studly($namePart.'_'.$datePart);
    }

    /**
     * Require migration file.
     *
     * @param string $file
     * @return void
     */
    protected function requireMigrationFile($file)
    {
        $this->files->requireFile($this->dir . '/' . $file . '.php');
    }
}
