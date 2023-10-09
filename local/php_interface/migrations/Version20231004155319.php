<?php

namespace Sprint\Migration;


class Version20231004155319 extends Version
{
    protected $description = "101021 | Добавить миграцию для установки цен / рубрик | установка количества для номеров";

    protected $moduleVersion = "4.4.1";

    public function up()
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('sale');

        $elements = \Bitrix\Iblock\Elements\ElementGlampingsTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => [],
        ])->fetchAll();

        foreach ($elements as $element) {
            $arElements[] = $element['ID'];
        }

        foreach ($arElements as $item) {
            $arFields = Array(
                "ID" => $item,
                "QUANTITY" => 999,	    
            );
            \CCatalogProduct::Add($arFields);
        }
    }

    public function down()
    {
        //your code ...
    }
}
