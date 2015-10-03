<?php

namespace Arrilot\BitrixMigrations\Interfaces;

interface FileRepositoryInterface
{
    /**
     * Get all of the migration files in a given path.
     *
     * @param  string  $path
     * @return array
     */
    public function getMigrationFiles($path);

    /**
     * Require a file.
     *
     * @param $path
     * @return void
     */
    public function requireFile($path);
}