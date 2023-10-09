<?php

namespace Sprint\Migration;


class Version20231004152907 extends Version
{
    protected $description = "101021 | Добавить миграцию для установки цен / рубрик | установка цен для номеров";

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
                "PRODUCT_ID" => $item,
                "CATALOG_GROUP_ID" => 1,
                "PRICE" => 10000,
                "CURRENCY" => "RUB",	    
            );
            $res = \CPrice::GetList(
                    array(),
                    array(
                            "PRODUCT_ID" => $item,
                            "CATALOG_GROUP_ID" => 1
                        )
                );
            if ($arr = $res->Fetch())
            {
                \CPrice::Update($arr["ID"], $arFields);
            }
            else
            {
                \CPrice::Add($arFields);
            }	
        }
    }

    public function down()
    {
        //your code ...
    }
}
