<?php

namespace Local\AddObjectBronevik\Orm;
use Bitrix\Main\SystemException;

class AddObjectBronevikTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_bronevik_advance_hotels';
    }

    /**
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID',
            ],
            'NAME' => [
                'data_type' => 'text',
                'title' => 'NAME'
            ],
            'CODE' => [
                'data_type' => 'text',
                'title' => 'CODE'
            ],
            'ADDRESS' => [
                'data_type' => 'text',
                'title' => 'ADDRESS'
            ],
            'CITY' => [
                'data_type' => 'text',
                'title' => 'CITY'
            ],
            'COUNTRY' => [
                'data_type' => 'text',
                'title' => 'COUNTRY'
            ],
            'ZIP' => [
                'data_type' => 'text',
                'title' => 'ZIP'
            ],
            'LAT' => [
                'data_type' => 'text',
                'title' => 'LAT'
            ],
            'LON' => [
                'data_type' => 'text',
                'title' => 'LON'
            ],
            'DESCRIPTION' => [
                'data_type' => 'text',
                'title' => 'DESCRIPTION'
            ],
            'PHOTOS' => [
                'data_type' => 'text',
                'title' => 'PHOTOS'
            ],
            'LAST_MODIFIED' => new \Bitrix\Main\ORM\Fields\DatetimeField('LAST_MODIFIED', array(
                'default_value' => function()
                {
                    return new \Bitrix\Main\Type\DateTime();
                },
                'title' => 'last modified',
            )),
        ];
    }

}