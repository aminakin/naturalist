<?php

namespace Sprint\Migration;


class Version20250402141358 extends Version
{
    protected $author = "admin2";

    protected $description = "121871 | Парсинг отзывов / Корректировка верстки для вывода виджита | HL блок отзывы Yandex";

    protected $moduleVersion = "4.18.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
    $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'YandexReviews',
  'TABLE_NAME' => 'b_hlbd_yandex_reviews',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'отзывы Yandex',
    ),
    'en' => 
    array (
      'NAME' => 'reviews Yandex',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_ID_OBJECT',
  'USER_TYPE_ID' => 'double',
  'XML_ID' => 'UF_ID_OBJECT',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'PRECISION' => 4,
    'SIZE' => 20,
    'MIN_VALUE' => 0.0,
    'MAX_VALUE' => 0.0,
    'DEFAULT_VALUE' => NULL,
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'id объекта',
    'ru' => 'id объекта',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'id объекта',
    'ru' => 'id объекта',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'id объекта',
    'ru' => 'id объекта',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'id объекта',
    'ru' => 'id объекта',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'id объекта',
    'ru' => 'id объекта',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_ID_YANDEX',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'UF_ID_YANDEX',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 0,
    'MAX_LENGTH' => 0,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'id отзыва ynadex',
    'ru' => 'id отзыва ynadex',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'id отзыва ynadex',
    'ru' => 'id отзыва ynadex',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'id отзыва ynadex',
    'ru' => 'id отзыва ynadex',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => 'id отзыва ynadex',
    'ru' => 'id отзыва ynadex',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => 'id отзыва ynadex',
    'ru' => 'id отзыва ynadex',
  ),
));
        }
}
