<?php

namespace Arrilot\BitrixMigrations\Autocreate\Handlers;

use Arrilot\BitrixMigrations\Autocreate\Manager;
use Arrilot\BitrixMigrations\Autocreate\Notifier;
use Arrilot\BitrixMigrations\Migrator;
use Bitrix\Main\Entity\EventResult;

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

    /**
     * Magic call to handler.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (! Manager::isTurnedOn()) {
            return new EventResult();
        }

        return call_user_func_array([$this, $method.'Handler'], $parameters);
    }
}
