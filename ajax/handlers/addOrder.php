<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Bitrix\Main\Application;
use Naturalist\Orders;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

$orders = new Orders();

if ($_REQUEST["params"]) {
	$params = $_REQUEST["params"];
	$res = $orders->add($params);
	echo $res;
} else if ($_REQUEST['action'] == 'couponAdd') {
	$res = $orders->enterCoupon($_REQUEST['coupon']);
	echo $res;
} else if ($_REQUEST['action'] == 'couponDelete') {
	$res = $orders->removeCoupon($_REQUEST['coupon']);
	echo $res;
}