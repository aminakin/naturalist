<?php

namespace Sprint\Migration;


class Version20241017154534 extends Version
{
    protected $description = "115635 | Сайт / Работа с блоком фотографий в админке сайта | пользовательские поля ";

    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'IBLOCK_catalog:catalog_SECTION',
  'FIELD_NAME' => 'UF_WINTER_PHOTOS',
  'USER_TYPE_ID' => 'file',
  'XML_ID' => 'UF_WINTER_PHOTOS',
  'SORT' => '100',
  'MULTIPLE' => 'Y',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'LIST_WIDTH' => 0,
    'LIST_HEIGHT' => 0,
    'MAX_SHOW_SIZE' => 0,
    'MAX_ALLOWED_SIZE' => 0,
    'EXTENSIONS' => 
    array (
    ),
    'TARGET_BLANK' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Изображения зима',
    'ru' => 'Изображения зима',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Изображения зима',
    'ru' => 'Изображения зима',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Изображения зима',
    'ru' => 'Изображения зима',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Изображения зима',
    'ru' => 'Изображения зима',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Изображения зима',
    'ru' => 'Изображения зима',
  ),
));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'IBLOCK_catalog:catalog_SECTION',
  'FIELD_NAME' => 'UF_MIDSEASON_PHOTOS',
  'USER_TYPE_ID' => 'file',
  'XML_ID' => 'UF_MIDSEASON_PHOTOS',
  'SORT' => '100',
  'MULTIPLE' => 'Y',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'LIST_WIDTH' => 0,
    'LIST_HEIGHT' => 0,
    'MAX_SHOW_SIZE' => 0,
    'MAX_ALLOWED_SIZE' => 0,
    'EXTENSIONS' => 
    array (
    ),
    'TARGET_BLANK' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Изображения осень+весна',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Изображения осень+весна',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Изображения осень+весна',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Изображения осень+весна',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => 'Изображения осень+весна',
  ),
));
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'ASD_IBLOCK',
  'FIELD_NAME' => 'UF_SEASON',
  'USER_TYPE_ID' => 'enumeration',
  'XML_ID' => '',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DISPLAY' => 'LIST',
    'LIST_HEIGHT' => 3,
    'CAPTION_NO_VALUE' => '',
    'SHOW_NO_VALUE' => 'N',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Сезон',
    'ru' => 'Сезон',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Сезон',
    'ru' => 'Сезон',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Сезон',
    'ru' => 'Сезон',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'Сезон',
    'ru' => 'Сезон',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'Сезон',
    'ru' => 'Сезон',
  ),
  'ENUM_VALUES' => 
  array (
    0 => 
    array (
      'VALUE' => 'Лето',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'summer',
    ),
    1 => 
    array (
      'VALUE' => 'Зима',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'winter',
    ),
    2 => 
    array (
      'VALUE' => 'Осень+Весна',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'mid-season ',
    ),
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
