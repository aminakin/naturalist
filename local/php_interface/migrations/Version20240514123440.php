<?php


namespace Sprint\Migration;


use Bitrix\Sale\Internals\OrderPropsGroupTable;
use Bitrix\Sale\Internals\OrderPropsTable;

class Version20240514123440 extends Version
{
    protected $author = "admin";

    protected $description = "123440 | Свойство заказа доброшрифт";
    protected $moduleVersion = "4.4.1";

    public function up()
    {
        \CModule::IncludeModule("sale");
        $personType = \Bitrix\Sale\PersonTypeTable::getList([
            'filter' => [
                'ACTIVE' => 'Y',
            ],
        ])->fetch();

        $group = OrderPropsGroupTable::getList([])->fetch();

        if ($personType !== false && $group !== false) {
            $arFields = [
                'PERSON_TYPE_ID' => $personType['ID'],
                "NAME" => "Доброшрифт:",
                "TYPE" => "Y/N",
                "REQUIRED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 100,
                "CODE" => "DOBRO_CERT",
                "USER_PROPS" => "N",
                "IS_LOCATION" => "N",
                "PROPS_GROUP_ID" => $group['ID'],
                "DESCRIPTION" => "",
                "IS_EMAIL" => "N",
                "IS_PROFILE_NAME" => "N",
                "IS_PAYER" => "N",
                "IS_LOCATION4TAX" => "N",
                "IS_FILTERED" => "N",
                "IS_ZIP" => "N",
                "IS_PHONE" => "N",
                "ACTIVE" => "Y",
                "UTIL" => "N",
                "INPUT_FIELD_LOCATION" => 0,
                "MULTIPLE" => "N",
                "IS_ADDRESS" => "N",
                "IS_ADDRESS_FROM" => "N",
                "IS_ADDRESS_TO" => "N",
                "SETTINGS" => "a:0:{}",
                "ENTITY_REGISTRY_TYPE" => "ORDER",
                "XML_ID" => "",
                "ENTITY_TYPE" => "ORDER"
            ];
            OrderPropsTable::add($arFields);
        }
    }

    public function down()
    {
        \CModule::IncludeModule("sale");
        $arResult = OrderPropsTable::getList([
            'filter' => [
                'CODE' => 'DOBRO_CERT'
            ],
        ])->fetch();
        if ($arResult) {
            OrderPropsTable::delete($arResult['ID']);
        }
    }
}
