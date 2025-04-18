<?php

namespace Sprint\Migration;


class Version20250409162104 extends Version
{
    protected $author = "admin";

    protected $description = "122123 | Uhotels / Модуль загрузки данный по объекту | Добавлено код свойство для загрузки отеля";

    protected $moduleVersion = "5.0.0";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'IBLOCK_catalog:catalog_SECTION',
  'FIELD_NAME' => 'UF_EXTERNAL_SERVICE',
  'USER_TYPE_ID' => 'enumeration',
  'XML_ID' => '',
  'SORT' => '403',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'I',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'Y',
  'SETTINGS' => 
  array (
    'DISPLAY' => 'LIST',
    'LIST_HEIGHT' => 5,
    'CAPTION_NO_VALUE' => '',
    'SHOW_NO_VALUE' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Booking service',
    'ru' => 'Сервис бронирования',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Booking service',
    'ru' => 'Сервис бронирования',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Booking service',
    'ru' => 'Сервис бронирования',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'ENUM_VALUES' => 
  array (
    0 => 
    array (
      'VALUE' => 'Traveline',
      'DEF' => 'N',
      'SORT' => '1',
      'XML_ID' => 'traveline',
    ),
    1 => 
    array (
      'VALUE' => 'Bnovo',
      'DEF' => 'Y',
      'SORT' => '2',
      'XML_ID' => 'bnovo',
    ),
    2 => 
    array (
      'VALUE' => 'bronevik',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'bronevik',
    ),
    3 => 
    array (
      'VALUE' => 'Uhotels',
      'DEF' => 'N',
      'SORT' => '600',
      'XML_ID' => 'uhotels',
    ),
  ),
));
    }

}
