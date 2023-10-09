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
$context = Application::getInstance()->getContext();
$request = $context->getRequest();

if ($request->get("params")) {
	$params = $request->get("params");
	$res = $orders->add($params);
	echo $res;
} else if ($request->get('action') == 'couponAdd') {
	$res = $orders->enterCoupon($request->get('coupon'));
	echo $res;
} else if ($request->get('action') == 'couponDelete') {
	$res = $orders->removeCoupon($request->get('coupon'));
	echo $res;
}