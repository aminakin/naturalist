<?php

namespace Sprint\Migration;


class Version20240328124728 extends Version
{
    protected $description = "108054 | Реализация доработок по файлу | Поле сео текста для разделов каталога";

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
  'FIELD_NAME' => 'UF_DOP_SEO_TEXT',
  'USER_TYPE_ID' => 'string',
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
    'SIZE' => 100,
    'ROWS' => 5,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'СЕО текст',
    'ru' => 'СЕО текст',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'СЕО текст',
    'ru' => 'СЕО текст',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'СЕО текст',
    'ru' => 'СЕО текст',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'СЕО текст',
    'ru' => 'СЕО текст',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'СЕО текст',
    'ru' => 'СЕО текст',
  ),
));
    }

    public function down()
    {
        //your code ...
    }
}
