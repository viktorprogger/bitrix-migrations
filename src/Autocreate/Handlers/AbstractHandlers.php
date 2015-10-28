<?php

namespace Arrilot\BitrixMigrations\Autocreate\Handlers;

use Arrilot\BitrixMigrations\Autocreate\Manager;
use Arrilot\BitrixMigrations\Autocreate\Notifier;
use Arrilot\BitrixMigrations\Migrator;

abstract class AbstractHandlers
{
    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * @var Notifier
     */
    protected $notifier;

    /**
     * Constructor.
     *
     * @param Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
        $this->notifier = new Notifier();
    }

    /**
     * Determine if autocreation is turned on.
     *
     * @return bool
     */
    protected function isTurnedOn()
    {
        return Manager::isTurnedOn();
    }

    /**
     * Create migration and apply it.
     *
     * @param string $name
     * @param string $template
     * @param array $replace
     *
     * @return bool
     */
    protected function createMigration($name, $template, $replace)
    {
        $migration = $this->migrator->createMigration(strtolower($name), $template, $replace);

        $this->migrator->logSuccessfulMigration($migration);

        $this->notifier->newMigration($migration);

        return true;
    }
}
