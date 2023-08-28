<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Naturalist\Baskets;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

$productId  = $_REQUEST["productId"];
$price      = $_REQUEST["price"];
$guests     = $_REQUEST["guests"];
$childrenAge = $_REQUEST["childrenAge"];
$dateFrom   = $_REQUEST["dateFrom"];
$dateTo     = $_REQUEST["dateTo"];
$externalId = $_REQUEST["externalId"];

$externalService = $_REQUEST["externalService"];
if($externalService == "bnovo") {
	$tariffId = $_REQUEST["tariffId"];
	$categoryId = $_REQUEST["categoryId"];
	$prices = $_REQUEST["prices"];

} else {
	$checksum = $_REQUEST["checksum"];
}

$count = 1;
$daysCount = (strtotime($dateTo) - strtotime($dateFrom)) / (60*60*24);
$arProps = array(
	[
		'CODE' => 'DATE_FROM',
		'VALUE' => $dateFrom
	],
	[
		'CODE' => 'DATE_TO',
		'VALUE' => $dateTo
	],
	[
		'CODE' => 'GUESTS_COUNT',
		'VALUE' => $guests
	],
	[
		'CODE' => 'CHILDREN',
		'VALUE' => $childrenAge
	],
	[
		'CODE' => 'DAYS_COUNT',
		'VALUE' => $daysCount
	],
	[
		'CODE' => 'EXTERNAL_ID',
		'VALUE' => $externalId
	],
	[
		'CODE' => 'EXTERNAL_SERVICE',
		'VALUE' => $externalService
	]
);
if($externalService == 'bnovo') {
	$arProps = array_merge($arProps, array(
		[
			'CODE' => 'TARIFF_ID',
			'VALUE' => $tariffId
		],
		[
			'CODE' => 'CATEGORY_ID',
			'VALUE' => $categoryId
		],
		[
			'CODE' => 'PRICES',
			'VALUE' => $prices
		]
	));

} else {
	$arProps = array_merge($arProps, array(
		[
			'CODE' => 'CHECKSUM',
			'VALUE' => $checksum
		],
	));
}

$basket = new Baskets();
//$res = $basket->deleteAll();

$res = $basket->add($productId, $count, $price, $arProps);
//xprint($res); die();
echo $res;