<?php

namespace Sprint\Migration;


class Version20230929125654 extends Version
{
    protected $description = "dev | Миграция после мерджа для API каталога";

    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->saveIblock(array (
  'IBLOCK_TYPE_ID' => 'catalog',
  'LID' => 
  array (
    0 => 's1',
  ),
  'CODE' => 'catalog',
  'API_CODE' => 'Glampings',
  'REST_ON' => 'N',
  'NAME' => 'Каталог',
  'ACTIVE' => 'Y',
  'SORT' => '500',
  'LIST_PAGE_URL' => '#SITE_DIR#/catalog/',
  'DETAIL_PAGE_URL' => '#SITE_DIR#/catalog/#SECTION_CODE#/#ELEMENT_CODE#/',
  'SECTION_PAGE_URL' => '#SITE_DIR#/catalog/#SECTION_CODE#/',
  'CANONICAL_PAGE_URL' => '',
  'PICTURE' => NULL,
  'DESCRIPTION' => '',
  'DESCRIPTION_TYPE' => 'text',
  'RSS_TTL' => '24',
  'RSS_ACTIVE' => 'Y',
  'RSS_FILE_ACTIVE' => 'N',
  'RSS_FILE_LIMIT' => NULL,
  'RSS_FILE_DAYS' => NULL,
  'RSS_YANDEX_ACTIVE' => 'N',
  'XML_ID' => NULL,
  'INDEX_ELEMENT' => 'Y',
  'INDEX_SECTION' => 'Y',
  'WORKFLOW' => 'N',
  'BIZPROC' => 'N',
  'SECTION_CHOOSER' => 'L',
  'LIST_MODE' => 'S',
  'RIGHTS_MODE' => 'E',
  'SECTION_PROPERTY' => 'N',
  'PROPERTY_INDEX' => 'N',
  'VERSION' => '2',
  'LAST_CONV_ELEMENT' => '0',
  'SOCNET_GROUP_ID' => NULL,
  'EDIT_FILE_BEFORE' => '',
  'EDIT_FILE_AFTER' => '',
  'SECTIONS_NAME' => 'Разделы',
  'SECTION_NAME' => 'Раздел',
  'ELEMENTS_NAME' => 'Элементы',
  'ELEMENT_NAME' => 'Элемент',
  'EXTERNAL_ID' => NULL,
  'LANG_DIR' => '/',
  'SERVER_NAME' => 'naturalist.travel',
  'IPROPERTY_TEMPLATES' => 
  array (
    'SECTION_META_TITLE' => '#TYPE# {=this.Name} в {=this.property.region_prepos} снять по доступной цене – Натуралист',
    'SECTION_META_DESCRIPTION' => 'Снять #TYPE# {=this.Name} в {=this.property.region_prepos} по цене от {=this.property.min_price} руб. {=this.Name}: подробное описание, фото, отзывы, оплата и моментальное онлайн-бронирование на сайте Натуралист.',
    'SECTION_PAGE_TITLE' => '#TYPE# {=this.Name}',
  ),
  'ELEMENT_ADD' => 'Добавить элемент',
  'ELEMENT_EDIT' => 'Изменить элемент',
  'ELEMENT_DELETE' => 'Удалить элемент',
  'SECTION_ADD' => 'Добавить раздел',
  'SECTION_EDIT' => 'Изменить раздел',
  'SECTION_DELETE' => 'Удалить раздел',
));

    }

    public function down()
    {
        //your code ...
    }
}
