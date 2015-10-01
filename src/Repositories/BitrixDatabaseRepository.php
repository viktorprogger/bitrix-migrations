<?php

namespace Arrilot\BitrixMigrations\Repositories;

class BitrixDatabaseRepository implements DatabaseRepositoryInterface
{
    /**
     * Bitrix $DB object.
     *
     * @var \CDatabase
     */
    protected $db;

    /**
     * BitrixDatabaseRepository constructor.
     */
    public function __construct()
    {
        global $DB;

        $this->db = $DB;
    }

    /**
     * Check if a given table already exists.
     *
     * @param $table
     * @return bool
     */
    public function checkTableExistence($table)
    {
        return (bool) $this->db->query('SHOW TABLES LIKE "'.$table.'"')->fetch();
    }

    /**
     * Create migration table.
     *
     * @param $table
     * @return void
     */
    public function createMigrationTable($table)
    {
        $this->db->query("CREATE TABLE {$table} (ID INT NOT NULL AUTO_INCREMENT, MIGRATION VARCHAR(255) NOT NULL, PRIMARY KEY (ID))");
    }
}