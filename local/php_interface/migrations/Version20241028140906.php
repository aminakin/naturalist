<?php

namespace Sprint\Migration;


class Version20241028140906 extends Version
{
    protected $description = "115530 | Тарифы / Привязка определённых тарифов к определённым номерам у объекта | свойство каталога для тарифов";

    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('catalog', 'catalog');
        $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Тарифы заданы вручную',
  'ACTIVE' => 'Y',
  'SORT' => '13',
  'CODE' => 'MANUAL_TARIF',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'L',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'C',
  'MULTIPLE' => 'N',
  'XML_ID' => NULL,
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '0',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'N',
  'IS_REQUIRED' => 'N',
  'VERSION' => '2',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
  'VALUES' => 
  array (
    0 => 
    array (
      'VALUE' => 'Y',
      'DEF' => 'N',
      'SORT' => '500',
      'XML_ID' => 'Y',
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

    public function down()
    {
        //your code ...
    }
}
