<?php

namespace Sprint\Migration;


use Bitrix\Sale\Internals\OrderPropsGroupTable;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\Internals\OrderPropsVariantTable;
use Bitrix\Sale\PersonTypeTable;

class order_bronevik_status20250228114807 extends Version
{
    protected $author = "admin";

    protected $description = "";

    protected $moduleVersion = "4.12.6";

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
                "NAME" => "Статус Броневик",
                "TYPE" => "ENUM",
                "REQUIRED" => "N",
                "DEFAULT_VALUE" => "",
                "SORT" => 100,
                "CODE" => "BRONEVIK_STATUS",
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
            $result = OrderPropsTable::add($arFields);
            if ($result->isSuccess()) {
                $propertyId = $result->getId();
                if ($propertyId > 0) {
                    $values = [
                        1 => 'Новый',
                        2 => 'В обработке',
                        3 => 'Ожидает подтверждения',
                        4 => 'Подтвержден',
                        5 => 'Не подтвержден',
                        6 => 'Ожидает подтверждения клиента',
                        7 => 'Заказана аннуляция',
                        8 => 'Ожидает аннуляции',
                        9 => 'Аннулировано, без штрафа',
                        10 => 'Аннулировано, штраф',
                    ];

                    // Добавляем каждое значение в свойство
                    foreach ($values as $key => $value) {
                        $variantFields = [
                            "ORDER_PROPS_ID" => $propertyId,
                            "VALUE" => $key,
                            "NAME" => $value,
                            "DESCRIPTION" => "",
                            "SORT" => 100,
                            "XML_ID" => "",
                        ];
                        OrderPropsVariantTable::add($variantFields);
                    }

                    echo "Свойство успешно создано и заполнено значениями";
                } else {
                    echo "Ошибка при создании свойства: "/* . CSaleOrderProps::GetLastError()*/ ;
                }
            }
        }
    }

    public function down()
    {
        \CModule::IncludeModule("sale");
        $arResult = OrderPropsTable::getList([
            'filter' => [
                'CODE' => 'BRONEVIK_STATUS', 'PERSON_TYPE_ID' => 1
            ],
        ])->fetch();
        if ($arResult) {
            $variants = OrderPropsVariantTable::getList([
                'filter' => [
                    'ORDER_PROPS_ID' => $arResult['ID'],
                ],
            ])->fetchAll();
            if ($variants) {
                foreach ($variants as $variant) {
                    OrderPropsVariantTable::delete($variant['ID']);
                }
            }
            OrderPropsTable::delete($arResult['ID']);
        }
    }
}
