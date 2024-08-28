<?php

use \Bitrix\Conversion\Internals\MobileDetect;

\Bitrix\Main\Loader::includeModule('conversion');

$detect = new MobileDetect;
global $isMobile;
$isMobile = $detect->isMobile();

/*
 * Форматированный вывод
 */
function xprint($a) {
    if (\Bitrix\Main\Engine\CurrentUser::get()->getId() == 3) {
        echo '<pre>';
        print_r($a);
        echo '</pre>';
    }
}

/*
 * Получает XML_ID варианта свойства по его ID
 * Возвращает false, если не найдено ничего или некорректные входные данные
 */
function getEnumXmlById($id)
{
    if (intval($id) > 0) {
        $arEnum = \CIBlockPropertyEnum::GetByID($id);
        if (is_array($arEnum)) {
            return $arEnum['XML_ID'];
        }

        return false;
    }

    return false;
}

/*
 * Получает ID варианта свойства по его XML_ID
 * Возвращает false, если не найдено ничего или некорректные входные данные
 */
function getEnumIdByXml($iblockId, $propertyCode, $xmlId)
{
    $rsEnum = \CIBlockPropertyEnum::GetList(
        ['SORT' => 'ASC'],
        [
            'IBLOCK_ID' => $iblockId,
            'CODE'      => $propertyCode,
            'XML_ID'    => $xmlId,
        ]
    );

    if ($arEnum = $rsEnum->Fetch()) {
        return $arEnum['ID'];
    } else {
        return false;
    }
}

/*
 * Получение ID свойства по его символьному коду
 * Возвращает false, если не найдено ничего или некорректные входные данные
 */
function getPropIdByCode($propertyCode, $iblockId)
{
    $rsProp = \CIBlockProperty::GetList(
        array(),
        array(
            "IBLOCK_ID" => $iblockId,
            "CODE"      => $propertyCode,
        )
    );

    if ($arProp = $rsProp->Fetch()) {
        return $arProp['ID'];
    } else {
        return false;
    }
}

function getMetaTags()
{

    $rsElements = CIBlockElement::GetList(
        array("SORT" => "ASC"),
        array(
            "IBLOCK_ID" => METATAGS_IBLOCK_ID,
            "ACTIVE" => "Y",
        ),
        false,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "IBLOCK_SECTION_ID",
            "CODE",
            "NAME",
            "PREVIEW_PICTURE",
            "PREVIEW_TEXT",
            "DETAIL_TEXT",
            "PROPERTY_TITLE",
            "PROPERTY_DESCRIPTION",
            "PROPERTY_H1",
            "PROPERTY_SEO_TEXT",
            "PROPERTY_URL",
        )
    );

    while ($arElement = $rsElements->GetNext()) {
		$keyURL = $arElement["PROPERTY_URL_VALUE"];
        $arResult[$keyURL] = $arElement;
    }

    return $arResult;
}

function plural_form($number, $after) { // Склонение слов
	$cases = array (2, 0, 1, 1, 1, 2);
	return $number.' '.$after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}

// Возвращает порядковый номер ребёнка прописью
function getChildrenOrderTitle($order) {
    switch ($order) {
        case 1:
            return 'первого';
        case 2:
            return 'второго';
        case 3:
            return 'третьего';
        case 4:
            return 'четвертого';
        case 5:
            return 'пятого';
        case 6:
            return 'шестого';
        case 7:
            return 'седьмого';
        case 8:
            return 'восьмого';
        case 9:
            return 'девятого';
        case 10:
            return 'десятого';
        case 11:
            return 'одиннадцатого';
        case 12:
            return 'двенадцатого';
        case 13:
            return 'тринадцатого';
        case 14:
            return 'четырнадцатого';
        case 15:
            return 'пятнадцатого';
        case 16:
            return 'шестнадцатого';
        case 17:
            return 'семнадцатого';
        case 18:
            return 'восемнадцатого';
        case 19:
            return 'девятнадцатого';
        case 20:
            return 'двадцатого';    
        default:
            return $order.'-го';
    }
}