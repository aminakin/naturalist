<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Bitrix\Main\Application;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Context;
use Bitrix\Sale\Order;
//use CIBlockSection;
//use CIBlockElement;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

CModule::includeModule("iblock");

$params = $_REQUEST["params"];

// Тарифы
$arTariffsValue = CIBlockElement::GetList(
    array("ID" => "ASC"),
    array(
        "IBLOCK_ID" => TARIFFS_IBLOCK_ID,
        "ACTIVE" => "Y",
        "PROPERTY_EXTERNAL_ID" => $params['tariffId'],
    ),
    false,
    false,
    array("IBLOCK_ID", "ID", "NAME", "PROPERTY_EXTERNAL_ID", "PROPERTY_CANCELLATION_RULES", "PROPERTY_CANCELLATION_DEADLINE", "PROPERTY_CANCELLATION_FINE_TYPE", "PROPERTY_CANCELLATION_FINE_AMOUNT")
)->Fetch();
echo json_encode($arTariffsValue);