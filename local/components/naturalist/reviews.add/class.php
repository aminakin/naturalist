<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

class ReviewsAdd extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    protected function prepareResultArray()
    {
        Loader::includeModule("iblock");

        /* Список свойств ИБ Отзывы */
        $rsProps = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => REVIEWS_IBLOCK_ID));
        while ($arProp = $rsProps->GetNext()) {
            $this->arResult[$arProp["CODE"]] = $arProp["NAME"];
        }
    }

    public function executeComponent()
    {
        $this->prepareResultArray();
        $this->includeComponentTemplate();
    }
}
