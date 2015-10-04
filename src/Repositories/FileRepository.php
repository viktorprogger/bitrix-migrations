<?php

namespace Arrilot\BitrixMigrations\Repositories;

use Arrilot\BitrixMigrations\Interfaces\FileRepositoryInterface;
use Exception;

class FileRepository implements FileRepositoryInterface
{
    /**
     * Get all of the migration files in a given path.
     *
     * @param string $path
     *
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
     *
     * @return void
     */
    public function requireFile($path)
    {
        require $path;
    }

    /**
     * Create a directory if it does not exist.
     *
     * @param $dir
     *
     * @return void
     */
    public function createDirIfItDoesNotExist($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Get the content of a file.
     *
     * @param string $path
     *
     * @throws Exception
     *
     * @return string
     */
    public function getContent($path)
    {
        if (!file_exists($path)) {
            throw new Exception("File does not exist at path {$path}");
        }

        return file_get_contents($path);
    }

    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @param bool   $lock
     *
     * @return int
     */
    public function putContent($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }
}
