<?php

namespace Arrilot\BitrixMigrations;

use Bitrix\Main\EventManager;
use CIBlock;
use CIBlockProperty;
use CUserTypeEntity;

class MigrationAutoCreator
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
            'OnAfterIBlockAdd',
            'OnAfterIBlockDelete',
            'OnAfterIBlockPropertyAdd',
            //'OnAfterIBlockPropertyUpdate',
            'OnBeforeIBlockPropertyDelete',
        ],
        'main' => [
            'OnBeforeUserTypeAdd',
            //'OnBeforeUserTypeUpdate',
            'OnBeforeUserTypeDelete',
        ]
    ];

    /**
     * Migrator instance.
     *
     * @var Migrator
     */
    protected static $migrator;

    /**
     * Register autocreator.
     *
     * @param $config
     */
    public static function register($config)
    {
        $templates = new TemplatesCollection($config);
        $templates->registerAutoTemplates();

        static::$migrator = new Migrator($config, $templates);

        static::addEventHandlers();

        static::turnOn();
    }

    /**
     * Add event handlers
     *
     * @return void
     */
    protected static function addEventHandlers()
    {
        $manager = EventManager::getInstance();

        foreach (static::$events as $module => $events) {
            foreach ($events as $event) {
                $manager->addEventHandler($module, $event, [__CLASS__, $event], false, 5000);
            }
        }
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
     * Create migration and apply it if needed.
     * Return false if migration was not applied.
     *
     * @param string $name
     * @param string $template
     * @param array $replace
     *
     * @return bool
     */
    protected function createMigration($name, $template, $replace)
    {
        $migration = static::$migrator->createMigration($name, $template, $replace);

        static::$migrator->logSuccessfulMigration($migration);

        return true;
    }

    /**
     * Create migration onAfterIBlockAdd.
     *
     * @param array $arFields
     * @return bool
     */
    public static function onAfterIBlockAdd(&$arFields)
    {
        if (!static::isTurnedOn() || !$arFields['ID']) {
            return true;
        }

        $fields = $arFields;
        unset($fields['ID'], $fields['RESULT'], $fields['RESULT_MESSAGE']);

        $template = "auto_add_iblock";
        $name = "{$template}_{$fields['CODE']}";

        $replace = [
            'fields' => var_export($fields, true),
            'code' => "'".$fields['CODE']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

    /**
     * Create migration onAfterIBlockDelete.
     *
     * @param int $id
     * @return bool
     */
    public static function onAfterIBlockDelete($id)
    {
        if (!self::isTurnedOn()) {
            return true;
        }

        $filter = [
            'ID' => $id,
            'CHECK_PERMISSIONS' => 'N',
        ];
        $fields = (new CIBlock())->GetList([], $filter)->fetch();

        $template = "auto_delete_iblock";
        $name = "{$template}_{$fields['CODE']}";

        $replace = [
            'code' => "'".$fields['CODE']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

    /**
     * Create migration onAfterIBlockPropertyAdd.
     *
     * @param array $arFields
     * @return bool
     */
    public static function onAfterIBlockPropertyAdd(&$arFields)
    {
        if (!static::isTurnedOn() || !$arFields['ID']) {
            return true;
        }

        $fields = $arFields;
        unset($fields['ID'], $fields['RESULT'], $fields['RESULT_MESSAGE']);

        $template = "auto_add_iblock_element_property";
        $name = "{$template}_{$fields['CODE']}_to_ib_{$fields['IBLOCK_ID']}";

        $replace = [
            'fields' => var_export($fields, true),
            'iblockId' => $fields['IBLOCK_ID'],
            'code' => "'".$fields['CODE']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

//    /**
//     * Create migration onAfterIBlockPropertyUpdate.
//     *
//     * @param array $fields
//     * @return bool
//     */
//    public static function onAfterIBlockPropertyUpdate(&$fields)
//    {
//        if (!static::isTurnedOn()) {
//            return true;
//        }
//
//        $template = "auto_update_iblock_element_property";
//        $name = "{$template}_{$fields['CODE']}_in_ib_{$fields['IBLOCK_ID']}";
//
//        $replace = [
//            'fields' => var_export($fields, true),
//            'iblockId' => $fields['IBLOCK_ID'],
//            'code' => "'".$fields['CODE']."'",
//        ];
//
//        return static::createMigration($name, $template, $replace);
//    }

    /**
     * Create migration onBeforeIBlockPropertyDelete.
     *
     * @param int $id
     * @return bool
     */
    public static function onBeforeIBlockPropertyDelete($id)
    {
        if (!self::isTurnedOn()) {
            return true;
        }

        $fields = CIBlockProperty::getByID($id)->fetch();

        $template = "auto_delete_iblock_element_property";
        $name = "{$template}_{$fields['CODE']}_in_ib_{$fields['IBLOCK_ID']}";

        $replace = [
            'iblockId' => $fields['IBLOCK_ID'],
            'code' => "'".$fields['CODE']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

    /**
     * Create migration OnBeforeUserTypeAdd.
     *
     * @param array $arFields
     * @return bool
     */
    public static function OnBeforeUserTypeAdd(&$arFields)
    {
        if (!static::isTurnedOn()) {
            return true;
        }

        $fields = $arFields;
        unset($fields['ID'], $fields['RESULT'], $fields['RESULT_MESSAGE']);

        $template = "auto_add_uf";
        $name = "{$template}_{$fields['FIELD_NAME']}_to_entity_{$fields['ENTITY_ID']}";

        $replace = [
            'fields' => var_export($fields, true),
            'code' => "'".$fields['FIELD_NAME']."'",
            'entity' => "'".$fields['ENTITY_ID']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

    /**
     * Note: bitrix does not pass ID in $arFields. This handler is totally useless atm.
     *
     * Create migration OnBeforeUserTypeUpdate.
     *
     * @param array $arFields
     * @return bool
     */
    public static function OnBeforeUserTypeUpdate(&$arFields)
    {
        if (!static::isTurnedOn() || !$arFields['ID']) {
            return true;
        }

        $fields = $arFields;

        $property = CUserTypeEntity::getByID($arFields['ID']);

        $template = "auto_update_uf";
        $name = "{$template}_{$property['FIELD_NAME']}_in_entity_{$property['ENTITY_ID']}";

        $replace = [
            'fields' => var_export($fields, true),
            'code' => "'".$fields['FIELD_NAME']."'",
            'entity' => "'".$fields['ENTITY_ID']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

    /**
     * Create migration OnBeforeUserTypeDelete.
     *
     * @param int $id
     * @return bool
     */
    public static function OnBeforeUserTypeDelete($id)
    {
        if (!self::isTurnedOn()) {
            return true;
        }

        $fields = CUserTypeEntity::getByID($id);

        $template = "auto_delete_uf";
        $name = "{$template}_{$fields['FIELD_NAME']}_in_entity_{$fields['ENTITY_ID']}";

        $replace = [
            'iblockId' => $fields['IBLOCK_ID'],
            'code' => "'".$fields['FIELD_NAME']."'",
            'entity' => "'".$fields['ENTITY_ID']."'",
        ];

        return static::createMigration($name, $template, $replace);
    }

    /**
     * Determine if autocreation is turned on.
     *
     * @return bool
     */
    protected static function isTurnedOn()
    {
        return static::$isTurnedOn && defined('ADMIN_SECTION');
    }
}
