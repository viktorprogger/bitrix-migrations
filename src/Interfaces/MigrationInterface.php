<?php

namespace Arrilot\BitrixMigrations\Interfaces;

interface MigrationInterface
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up();

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down();
}