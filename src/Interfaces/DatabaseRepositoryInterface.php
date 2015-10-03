<?php

namespace Arrilot\BitrixMigrations\Interfaces;

interface DatabaseRepositoryInterface
{
    /**
     * Check if a given table already exists.
     *
     * @return bool
     */
    public function checkMigrationTableExistence();

    /**
     * Create migration table.
     *
     * @return void
     */
    public function createMigrationTable();

    /**
     * Get an array of migrations the have been ran previously.
     *
     * @return array
     */
    public function getRanMigrations();

    /**
     * Save migration name to the database to prevent it from running again.
     *
     * @param string $fileName
     * @return void
     */
    public function logSuccessfulMigration($fileName);
}