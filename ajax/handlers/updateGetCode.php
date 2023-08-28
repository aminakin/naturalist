<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */


use Bitrix\Main\Application;
use Naturalist\Users;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

global $arUser, $userId;
if($userId > 0) {
	$type = $_REQUEST['type'];
	$value = $_REQUEST['value'];

	$users = new Users();
	$res = $users->updateGetCode($type, $value);

	echo $res;

} else {
	echo json_encode([
		"ERROR" => "Ошибка доступа."
	]);
}