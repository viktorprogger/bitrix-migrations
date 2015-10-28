<?php

namespace Arrilot\BitrixMigrations\Autocreate\Handlers;

use CIBlock;
use CIBlockProperty;
use CUserTypeEntity;

class MainModuleHandlers extends AbstractHandlers
{
    /**
     * Create migration OnBeforeUserTypeAdd.
     *
     * @param array $fields
     * @return bool
     */
    public function OnBeforeUserTypeAdd(&$fields)
    {
        if (!$this->isTurnedOn()) {
            return true;
        }

        $template = "auto_add_uf";
        $name = "{$template}_{$fields['FIELD_NAME']}_to_entity_{$fields['ENTITY_ID']}";

        $replace = [
            'fields' => var_export($fields, true),
            'code' => "'".$fields['FIELD_NAME']."'",
            'entity' => "'".$fields['ENTITY_ID']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

//    /**
//     * Note: bitrix does not pass ID in $arFields. This handler is totally useless atm.
//     *
//     * Create migration OnBeforeUserTypeUpdate.
//     *
//     * @param array $fields
//     * @return bool
//     */
//    public function OnBeforeUserTypeUpdate(&$fields)
//    {
//        if (!$this->isTurnedOn()) {
//            return true;
//        }
//
//        $property = CUserTypeEntity::getByID($fields['ID']);
//
//        $template = "auto_update_uf";
//        $name = "{$template}_{$property['FIELD_NAME']}_in_entity_{$property['ENTITY_ID']}";
//
//        $replace = [
//            'fields' => var_export($fields, true),
//            'code' => "'".$fields['FIELD_NAME']."'",
//            'entity' => "'".$fields['ENTITY_ID']."'",
//        ];
//
//        return $this->createMigration($name, $template, $replace);
//    }

    /**
     * Create migration OnBeforeUserTypeDelete.
     *
     * @param int $id
     * @return bool
     */
    public function OnBeforeUserTypeDelete($id)
    {
        if (!$this->isTurnedOn()) {
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

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Delete old notification.
     */
    public function OnAfterEpilog()
    {
        $this->notifier->deleteNotificationFromPreviousMigration();
    }

}
