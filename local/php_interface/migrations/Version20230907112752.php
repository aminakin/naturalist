<?php

namespace Sprint\Migration;


class Version20230907112752 extends Version
{
    protected $description = "99374 | Поисковая выдача / Расчёт расстояний между объектами и вывод ближайших объектов в поиске  | Настройки ИБ секции";

    protected $moduleVersion = "4.4.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('catalog', 'catalog');
        $helper->UserOptions()->saveSectionForm($iblockId, array (
  'Раздел|edit1' => 
  array (
    'ID' => 'ID',
    'DATE_CREATE' => 'Создан',
    'TIMESTAMP_X' => 'Изменен',
    'ACTIVE' => 'Раздел активен',
    'IBLOCK_SECTION_ID' => 'Родительский раздел',
    'NAME' => 'Название',
    'CODE' => 'Символьный код',
    'UF_REGION_NAME' => 'Название региона',
    'PICTURE' => 'Изображение',
    'DESCRIPTION' => 'Описание',
  ),
  'SEO|edit5' => 
  array (
    'IPROPERTY_TEMPLATES_SECTION' => 'Настройки для разделов',
    'IPROPERTY_TEMPLATES_SECTION_META_TITLE' => 'Шаблон META TITLE',
    'IPROPERTY_TEMPLATES_SECTION_META_KEYWORDS' => 'Шаблон META KEYWORDS',
    'IPROPERTY_TEMPLATES_SECTION_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
    'IPROPERTY_TEMPLATES_SECTION_PAGE_TITLE' => 'Заголовок раздела',
    'IPROPERTY_TEMPLATES_ELEMENT' => 'Настройки для элементов',
    'IPROPERTY_TEMPLATES_ELEMENT_META_TITLE' => 'Шаблон META TITLE',
    'IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS' => 'Шаблон META KEYWORDS',
    'IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
    'IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE' => 'Заголовок товара',
    'IPROPERTY_TEMPLATES_SECTIONS_PICTURE' => 'Настройки для изображений разделов',
    'IPROPERTY_TEMPLATES_SECTION_PICTURE_FILE_ALT' => 'Шаблон ALT',
    'IPROPERTY_TEMPLATES_SECTION_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
    'IPROPERTY_TEMPLATES_SECTION_PICTURE_FILE_NAME' => 'Шаблон имени файла',
    'IPROPERTY_TEMPLATES_SECTIONS_DETAIL_PICTURE' => 'Настройки для детальных картинок разделов',
    'IPROPERTY_TEMPLATES_SECTION_DETAIL_PICTURE_FILE_ALT' => 'Шаблон ALT',
    'IPROPERTY_TEMPLATES_SECTION_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
    'IPROPERTY_TEMPLATES_SECTION_DETAIL_PICTURE_FILE_NAME' => 'Шаблон имени файла',
    'IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE' => 'Настройки для картинок анонса элементов',
    'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT' => 'Шаблон ALT',
    'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
    'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME' => 'Шаблон имени файла',
    'IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE' => 'Настройки для детальных картинок элементов',
    'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT' => 'Шаблон ALT',
    'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
    'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME' => 'Шаблон имени файла',
    'IPROPERTY_TEMPLATES_MANAGEMENT' => 'Управление',
    'IPROPERTY_CLEAR_VALUES' => 'Очистить кеш вычисленных значений',
  ),
  'Дополнительно|edit2' => 
  array (
    'SORT' => 'Сортировка',
    'DETAIL_PICTURE' => 'Детальная картинка',
  ),
  'Доп. поля|user_fields_tab' => 
  array (
    'USER_FIELDS_ADD' => 'Добавить пользовательское свойство',
    'user_fields_tab_csection2' => 'Настройки региона',
    'UF_REGION' => 'Привязка к справочнику регионов',
    'UF_REGION_PREPOS' => 'Регион в предложном падеже',
    'user_fields_tab_csection3' => 'Другое',
    'UF_AGENT' => 'Процент Агента',
    'UF_INN' => 'ИНН',
    'UF_PHOTOS' => 'UF_PHOTOS',
    'UF_ADDRESS' => 'UF_ADDRESS',
    'UF_COORDS' => 'UF_COORDS',
    'UF_DISTANCE' => 'UF_DISTANCE',
    'UF_PREVIEW_TEXT' => 'UF_PREVIEW_TEXT',
    'UF_TIME_FROM' => 'UF_TIME_FROM',
    'UF_TIME_TO' => 'UF_TIME_TO',
    'UF_MIN_PRICE' => 'UF_MIN_PRICE',
    'UF_MIN_AGE' => 'Возраст детей без места',
    'UF_NO_CHILDREN_PLACE' => 'Нет размещений с детьми из выгрузки',
    'UF_RESERVE_COUNT' => 'UF_RESERVE_COUNT',
    'UF_EXTERNAL_SERVICE' => 'UF_EXTERNAL_SERVICE',
    'UF_EXTERNAL_ID' => 'UF_EXTERNAL_ID',
    'UF_TOP' => 'UF_TOP',
    'UF_PREMIUM' => 'UF_PREMIUM',
    'UF_ACTION' => 'UF_ACTION',
    'UF_TYPE' => 'UF_TYPE',
    'UF_TYPE_EXTRA' => 'Дополнительный тип',
    'UF_SERVICES' => 'UF_SERVICES',
    'UF_FEATURES' => 'UF_FEATURES',
    'UF_PHONE' => 'Телефон',
    'UF_EMAIL' => 'Email',
    'UF_WEBSITE' => 'Сайт',
    'UF_ROOMS_COUNT' => 'Кол-во комнат',
    'UF_FOOD' => 'Питание',
    'UF_IMPRESSIONS' => 'Впечатления',
    'UF_EXTERNAL_UID' => 'Внешний ID (Bnovo)',
    'user_fields_tab_csection1' => 'Настройки импорта из Traveline',
    'UF_CHECKBOX_1' => 'Не выгружать изображения',
    'UF_CHECKBOX_2' => 'Не выгружать услуги',
    'UF_CHECKBOX_3' => 'Не выгружать преимущества',
    'UF_CHECKBOX_4' => 'Не выгружать кол-во номеров',
    'UF_CHECKBOX_5' => 'Не выгружать питание',
  ),
));
    $helper->UserOptions()->saveElementGrid($iblockId, array (
  'views' => 
  array (
    'default' => 
    array (
      'columns' => 
      array (
        0 => 'NAME',
        1 => 'ACTIVE',
        2 => 'SORT',
        3 => 'TIMESTAMP_X',
        4 => 'ID',
      ),
      'columns_sizes' => 
      array (
        'expand' => 1,
        'columns' => 
        array (
        ),
      ),
      'sticked_columns' => 
      array (
      ),
      'last_sort_by' => 'name',
      'last_sort_order' => 'asc',
      'custom_names' => 
      array (
      ),
      'page_size' => 500,
    ),
  ),
  'filters' => 
  array (
  ),
  'current_view' => 'default',
));
    $helper->UserOptions()->saveSectionGrid($iblockId, array (
  'views' => 
  array (
    'default' => 
    array (
      'columns' => 
      array (
        0 => 'NAME',
        1 => 'ACTIVE',
        2 => 'UF_REGION_PREPOS',
        3 => 'UF_REGION',
        4 => 'UF_ADDRESS',
        5 => 'UF_COORDS',
        6 => 'SORT',
        7 => 'TIMESTAMP_X',
        8 => 'ID',
        9 => 'UF_EXTERNAL_SERVICE',
        10 => 'UF_EXTERNAL_ID',
      ),
      'columns_sizes' => 
      array (
        'expand' => 1,
        'columns' => 
        array (
        ),
      ),
      'sticked_columns' => 
      array (
      ),
      'last_sort_by' => 'timestamp_x',
      'last_sort_order' => 'asc',
      'page_size' => 500,
      'custom_names' => 
      array (
      ),
    ),
  ),
  'filters' => 
  array (
  ),
  'current_view' => 'default',
));

    }

    public function down()
    {
        //your code ...
    }
}
