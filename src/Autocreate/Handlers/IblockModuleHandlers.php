<?php

namespace Arrilot\BitrixMigrations\Autocreate\Handlers;

use CIBlock;
use CIBlockProperty;
use CIBlockPropertyEnum;

class IblockModuleHandlers extends AbstractHandlers
{
    /**
     * Create migration OnBeforeIBlockAdd.
     *
     * @param array $fields
     * @return bool
     */
    public function OnBeforeIBlockAddHandler(&$fields)
    {
        $template = "auto_add_iblock";
        $name = "{$template}_{$fields['CODE']}";

        $replace = [
            'fields' => var_export($fields, true),
            'code' => "'".$fields['CODE']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Create migration OnBeforeIBlockAdd.
     *
     * @param array $fields
     * @return bool
     */
    public function OnBeforeIBlockUpdateHandler(&$fields)
    {
        $template = "auto_update_iblock";
        $name = "{$template}_{$fields['CODE']}";

        $replace = [
            'fields' => var_export($fields, true),
            'code' => "'".$fields['CODE']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Create migration OnBeforeIBlockDelete.
     *
     * @param int $id
     * @return bool
     */
    public function OnBeforeIBlockDeleteHandler($id)
    {
        $fields = $this->getIBlockById($id);

        $template = "auto_delete_iblock";
        $name = "{$template}_{$fields['CODE']}";

        $replace = [
            'code' => "'".$fields['CODE']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Create migration OnBeforeIBlockPropertyAdd.
     *
     * @param array $fields
     * @return bool
     */
    public function OnBeforeIBlockPropertyAddHandler(&$fields)
    {
        $template = "auto_add_iblock_element_property";
        $name = "{$template}_{$fields['CODE']}_to_ib_{$fields['IBLOCK_ID']}";

        $replace = [
            'fields' => var_export($fields, true),
            'iblockId' => $fields['IBLOCK_ID'],
            'code' => "'".$fields['CODE']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Create migration OnBeforeIBlockPropertyUpdate.
     *
     * @param array $fields
     * @return bool
     */
    public function OnBeforeIBlockPropertyUpdateHandler(&$fields)
    {
        $dbFields = $this->collectPropertyFieldsFromDB($fields['ID'], $fields['IBLOCK_ID']);

        if (!$this->propertyHasChanged($fields, $dbFields)) {
            return true;
        }

        $template = "auto_update_iblock_element_property";
        $name = "{$template}_{$fields['CODE']}_in_ib_{$fields['IBLOCK_ID']}";

        $replace = [
            'fields' => var_export($fields, true),
            'iblockId' => $fields['IBLOCK_ID'],
            'code' => "'".$fields['CODE']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Create migration onBeforeIBlockPropertyDelete.
     *
     * @param int $id
     * @return bool
     */
    public function OnBeforeIBlockPropertyDeleteHandler($id)
    {
        $fields = CIBlockProperty::getByID($id)->fetch();

        $template = "auto_delete_iblock_element_property";
        $name = "{$template}_{$fields['CODE']}_in_ib_{$fields['IBLOCK_ID']}";

        $replace = [
            'iblockId' => $fields['IBLOCK_ID'],
            'code' => "'".$fields['CODE']."'",
        ];

        return $this->createMigration($name, $template, $replace);
    }

    /**
     * Get iblock by id without checking permissions.
     *
     * @param $id
     * @return array
     */
    protected function getIBlockById($id)
    {
        $filter = [
            'ID' => $id,
            'CHECK_PERMISSIONS' => 'N',
        ];

        return (new CIBlock())->getList([], $filter)->fetch();
    }

    /**
     * Collect property fields from DB and convert them to format that can be compared from user input.
     *
     * @param $id
     * @param $iblockId
     *
     * @return array
     */
    protected function collectPropertyFieldsFromDB($id, $iblockId)
    {
        $fields = CIBlockProperty::getByID($id)->fetch();
        $fields['VALUES'] = [];

        $filter = [
            "IBLOCK_ID"   => $iblockId,
            "PROPERTY_ID" => $id,
        ];
        $sort = [
            "SORT" => "ASC",
            "VALUE" => "ASC",
        ];

        $propertyEnums = CIBlockPropertyEnum::GetList($sort, $filter);
        while($v = $propertyEnums->GetNext()) {
            $fields['VALUES'][$v['ID']] = [
                'ID' => $v['ID'],
                'VALUE' => $v['VALUE'],
                'SORT' => $v['SORT'],
                'XML_ID' => $v['XML_ID'],
                'DEF' => $v['DEF'],
            ];
        }

        return $fields;
    }

    /**
     * @param $fields
     * @param $dbFields
     *
     * @return bool
     */
    protected function propertyHasChanged($fields, $dbFields)
    {
        foreach ($dbFields as $field => $value) {
            if (isset($fields[$field]) && ($fields[$field] != $value)) {
                return true;
            }
        }

        return false;
    }
}
