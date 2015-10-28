<?php

namespace Arrilot\BitrixMigrations\Autocreate;

use Arrilot\BitrixMigrations\Autocreate\Handlers\AbstractHandlers;
use Arrilot\BitrixMigrations\Migrator;
use Arrilot\BitrixMigrations\TemplatesCollection;
use Bitrix\Main\EventManager;

class Manager
{
    /**
     * A flag that autocreation was turned on.
     *
     * @var bool
     */
    protected static $isTurnedOn = false;

    /**
     * Events that are used by autocreation.
     *
     * @var array
     */
    protected static $events = [
        'iblock' => [
            'OnBeforeIBlockAdd' => 'onBeforeIBlockAdd',
            'OnBeforeIBlockUpdate' => 'onBeforeIBlockUpdate',
            'OnBeforeIBlockDelete' => 'onBeforeIBlockDelete',
            'OnBeforeIBlockPropertyAdd' => 'onBeforeIBlockPropertyAdd',
            'OnBeforeIBlockPropertyUpdate' => 'onBeforeIBlockPropertyUpdate',
            'OnBeforeIBlockPropertyDelete' => 'onBeforeIBlockPropertyDelete',
        ],
        'main' => [
            'OnBeforeUserTypeAdd' => 'onBeforeUserTypeAdd',
            //'OnBeforeUserTypeUpdate' => 'onBeforeUserTypeUpdate',
            'OnBeforeUserTypeDelete' => 'onBeforeUserTypeDelete',
            'OnAfterEpilog' => 'onAfterEpilog',
        ],
        'highloadblock' => [
            '\\Bitrix\\Highloadblock\\Highloadblock::OnBeforeUpdate' => 'onBeforeUpdate',
        ]
    ];

    /**
     * Initialize autocreation.
     *
     * @param $config
     */
    public static function init($config)
    {
        $templates = new TemplatesCollection($config);
        $templates->registerAutoTemplates();

        static::addEventHandlers(new Migrator($config, $templates));

        static::turnOn();
    }

    /**
     * Determine if autocreation is turned on.
     *
     * @return bool
     */
    public static function isTurnedOn()
    {
        return static::$isTurnedOn && defined('ADMIN_SECTION');
    }

    /**
     * Turn on autocreation.
     *
     * @return void
     */
    public static function turnOn()
    {
        static::$isTurnedOn = true;
    }

    /**
     * Turn off autocreation.
     *
     * @return void
     */
    public static function turnOff()
    {
        static::$isTurnedOn = false;
    }

    /**
     * Add event handlers
     *
     * @param Migrator $migrator
     */
    protected static function addEventHandlers(Migrator $migrator)
    {
        $eventManager = EventManager::getInstance();

        foreach (static::$events as $module => $events) {
            $handlersObject = static::instantiateHandlersObjectForModule($module, $migrator);
            foreach ($events as $event => $handler) {
                $eventManager->addEventHandler($module, $event, [$handlersObject, $handler], false, 5000);
            }
        }
    }

    /**
     * Instantiate handlers class object.
     *
     * @param $module
     * @param $migrator
     *
     * @return AbstractHandlers
     */
    protected static function instantiateHandlersObjectForModule($module, $migrator)
    {
        $class = __NAMESPACE__.'\\Handlers\\'.ucfirst($module).'ModuleHandlers';

        return new $class($migrator);
    }
}
