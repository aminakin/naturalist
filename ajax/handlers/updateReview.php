<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Bitrix\Main\Application;
use Naturalist\Reviews;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

global $arUser, $userId;
if($userId > 0) {
	$reviewId = $_REQUEST['reviewId'];
	$params = ($_REQUEST['params']) ? json_decode($_REQUEST['params'], true) : [];

	$reviews = new Reviews();
	$res = $reviews->update($reviewId, $params);

	echo $res;

} else {
	echo json_encode([
		"ERROR" => "Ошибка доступа."
	]);
}