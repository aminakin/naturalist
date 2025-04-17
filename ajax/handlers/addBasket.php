<?

/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Naturalist\Baskets;
use Bitrix\Main\Application;
use Bitrix\Highloadblock\HighloadBlockTable;

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
$people = $_REQUEST["people"];
$title = $_REQUEST["title"];
$photo = isset($_REQUEST["photo"]) ? $_REQUEST["photo"] : '';
$bronevikOfferExternalId = isset($_REQUEST["bronevikOfferExternalId"]) ? $_REQUEST["bronevikOfferExternalId"] : '';
$uhotelsTariffId = isset($_REQUEST["uhotelsTariffId"]) ? $_REQUEST["uhotelsTariffId"] : '';

$externalService = $_REQUEST["externalService"];
if ($externalService == 'uhotels') {
    $prices = $_REQUEST["prices"];
    //
} elseif ($externalService == 'bronevik') {
    //
} elseif ($externalService == "bnovo") {
	$tariffId = $_REQUEST["tariffId"];
	$categoryId = $_REQUEST["categoryId"];
	$prices = $_REQUEST["prices"];
} else {
	$checksum = $_REQUEST["checksum"];
	$session = Application::getInstance()->getSession();
	$sessionId = $session->getId();

	$checksummDataClass = HighloadBlockTable::compileEntity(TRAVELINE_CHECKSUMM_HL_ENTITY)->getDataClass();
	$query = $checksummDataClass::query()
		->addSelect('ID')
		->where('UF_SESSION_ID', $sessionId)
		?->fetch();

	if (!empty($query)) {
		$checksummDataClass::update($query['ID'], ['UF_CHECKSUMM' => $checksum]);
	} else {
		$checksummDataClass::add([
			'UF_SESSION_ID' => $sessionId,
			'UF_CHECKSUMM' => $checksum,
		]);
	}
}

$count = 1;
$daysCount = (strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24);
$arProps = array(
	[
		'CODE' => 'DATE_FROM',
		'NAME' => 'Дата заезда',
		'VALUE' => $dateFrom
	],
	[
		'CODE' => 'DATE_TO',
		'NAME' => 'Дата выезда',
		'VALUE' => $dateTo,
	],
	[
		'CODE' => 'GUESTS_COUNT',
		'NAME' => 'Количество гостей',
		'VALUE' => $guests,
	],
	[
		'CODE' => 'CHILDREN',
		'NAME' => 'Возраст детей',
		'VALUE' => $childrenAge,
	],
	[
		'CODE' => 'DAYS_COUNT',
		'NAME' => 'Количество дней',
		'VALUE' => $daysCount,
	],
	[
		'CODE' => 'EXTERNAL_ID',
		'NAME' => 'Внешний ID',
		'VALUE' => $externalId,
	],
	[
		'CODE' => 'EXTERNAL_SERVICE',
		'NAME' => 'Внешний сервис',
		'VALUE' => $externalService,
	],
	[
		'CODE' => 'REAL_PRICE',
		'NAME' => 'Цена',
		'VALUE' => $price,
	],
	[
		'CODE' => 'PEOPLE',
		'NAME' => 'Размещение гостей',
		'VALUE' => $people,
	],
	[
		'CODE' => 'SESSION_ID',
		'NAME' => 'Сессия',
		'VALUE' => $sessionId,
	],
	[
		'CODE' => 'TITLE',
		'NAME' => 'Название номера',
		'VALUE' => $title,
	],
	[
		'CODE' => 'PHOTO',
		'NAME' => 'Фото номера',
		'VALUE' => $photo,
	]
);

if ($externalService == 'uhotels') {
    $arProps = array_merge($arProps, [
        [
            'CODE' => 'UHOTELS_TARIFF_ID',
            'NAME' => 'Ид тарифа uhotels',
            'VALUE' => $uhotelsTariffId,
        ],
        [
            'CODE' => 'PRICES',
            'VALUE' => $prices
        ]
    ]);
    //
} elseif ($externalService == 'bronevik') {
    $arProps = array_merge($arProps, [
        [
            'CODE' => 'BRONEVIK_OFFER_ID',
            'VALUE' => $bronevikOfferExternalId,
        ],
//        [
//            'CODE' => 'PRICES',
//            'VALUE' => $prices
//        ]
    ]);
} elseif ($externalService == 'bnovo') {
	$arProps = array_merge($arProps, array(
		[
			'CODE' => 'TARIFF_ID',
			'NAME' => 'ID тарифа Бново',
			'VALUE' => $tariffId
		],
		[
			'CODE' => 'CATEGORY_ID',
			'NAME' => 'ID номера Бново',
			'VALUE' => $categoryId
		],
		[
			'CODE' => 'PRICES',
			'VALUE' => $prices
		]
	));
} else {
	// $arProps = array_merge($arProps, array(
	// 	[
	// 		'CODE' => 'CHECKSUM',
	// 		'VALUE' => $checksum
	// 	]
	// ));
}

$basket = new Baskets();
$res = $basket->add($productId, $count, $price, $arProps);
echo $res;
