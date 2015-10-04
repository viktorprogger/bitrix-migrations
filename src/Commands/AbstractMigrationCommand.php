<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Interfaces\MigrationInterface;
use Illuminate\Support\Str;

abstract class AbstractMigrationCommand extends AbstractCommand
{
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
}
