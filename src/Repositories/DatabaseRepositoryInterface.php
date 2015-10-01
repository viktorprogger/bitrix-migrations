<?php

namespace Arrilot\BitrixMigrations\Repositories;

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
}