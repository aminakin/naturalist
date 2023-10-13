<?php

namespace Sprint\Migration;


class Version20231013091509 extends Version
{
    protected $description = "101310 | Баги | Добавлен столбец избранного в HL регионов";

    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'HLBLOCK_Regions',
  'FIELD_NAME' => 'UF_FAVORITE',
  'USER_TYPE_ID' => 'boolean',
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
    'DEFAULT_VALUE' => 0,
    'DISPLAY' => 'CHECKBOX',
    'LABEL' => 
    array (
      0 => '',
      1 => '',
    ),
    'LABEL_CHECKBOX' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Избранный регион',
    'ru' => 'Избранный регион',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Избранный регион',
    'ru' => 'Избранный регион',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Избранный регион',
    'ru' => 'Избранный регион',
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
));
    }

    public function down()
    {
        //your code ...
    }
}
