<?php

namespace Arrilot\BitrixMigrations\Repositories;

interface DatabaseRepositoryInterface
{
    /**
     * Check if a given table already exists.
     *
     * @param $table
     * @return bool
     */
    public function checkTableExistence($table);

    /**
     * Create migration table.
     *
     * @param $table
     * @return void
     */
    public function createMigrationTable($table);
}