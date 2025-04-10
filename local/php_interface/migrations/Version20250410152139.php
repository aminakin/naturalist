<?php

namespace Sprint\Migration;


class Version20250410152139 extends Version
{
    protected $author = "admin";

    protected $description = "122123 | Uhotels / Модуль загрузки данный по объекту | Добавлено код свойство для загрузки отеля номеров";

    protected $moduleVersion = "5.0.0";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('catalog', 'catalog');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Внешний сервис',
  'ACTIVE' => 'Y',
  'SORT' => '100',
  'CODE' => 'EXTERNAL_SERVICE',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'L',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'L',
  'MULTIPLE' => 'N',
  'XML_ID' => NULL,
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '0',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'Y',
  'IS_REQUIRED' => 'N',
  'VERSION' => '2',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
  'VALUES' => 
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
      'VALUE' => 'Bronevik',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'd8beb93dbb5e1ca4c1f21521d529573e',
    ),
    3 => 
    array (
      'VALUE' => 'Uhotels',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'uhotels',
    ),
  ),
  'FEATURES' => 
  array (
    0 => 
    array (
      'MODULE_ID' => 'catalog',
      'FEATURE_ID' => 'IN_BASKET',
      'IS_ENABLED' => 'N',
    ),
    1 => 
    array (
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
    2 => 
    array (
      'MODULE_ID' => 'iblock',
      'FEATURE_ID' => 'LIST_PAGE_SHOW',
      'IS_ENABLED' => 'N',
    ),
  ),
));
    
    }
}
