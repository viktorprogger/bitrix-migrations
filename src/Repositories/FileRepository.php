<?php

namespace Arrilot\BitrixMigrations\Repositories;

use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;

class FileRepository implements FileRepositoryInterface
{
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
     * Require a file.
     *
     * @param $path
     * @return void
     */
    public function requireFile($path)
    {
        require $path;
    }
}