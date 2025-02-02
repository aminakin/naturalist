<?php

namespace Sprint\Migration;


class Version20250129144219 extends Version
{
    protected $author = "admin2";

    protected $description = "118443 | Верстка \"Детальная карточка объекта\" | Категории для удобств номеров";

    protected $moduleVersion = "4.16.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'HLBLOCK_RoomFeatures',
  'FIELD_NAME' => 'UF_CATEGORY',
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
    'DISPLAY' => 'CHECKBOX',
    'LIST_HEIGHT' => 1,
    'CAPTION_NO_VALUE' => '',
    'SHOW_NO_VALUE' => 'Y',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Категория удобства',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Категория удобства',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => '',
    'ru' => 'Категория удобства',
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
      'VALUE' => 'Спальные места',
      'DEF' => 'N',
      'SORT' => '5',
      'XML_ID' => 'sleep',
    ),
    1 => 
    array (
      'VALUE' => 'Ванная комната',
      'DEF' => 'N',
      'SORT' => '10',
      'XML_ID' => 'bath',
    ),
    2 => 
    array (
      'VALUE' => 'Кухня',
      'DEF' => 'N',
      'SORT' => '15',
      'XML_ID' => 'kitchen',
    ),
    3 => 
    array (
      'VALUE' => 'Удобства',
      'DEF' => 'N',
      'SORT' => '20',
      'XML_ID' => 'comfort',
    ),
    4 => 
    array (
      'VALUE' => 'Что снаружи/рядом с домом',
      'DEF' => 'N',
      'SORT' => '25',
      'XML_ID' => 'outside',
    ),
    5 => 
    array (
      'VALUE' => 'Вид из номера',
      'DEF' => 'N',
      'SORT' => '30',
      'XML_ID' => 'view',
    ),
  ),
));
    }

}
