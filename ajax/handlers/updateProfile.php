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
	$params = $_REQUEST['params'];

	$users = new Users();
	$res = $users->update($params);

	echo $res;

} else {
	echo json_encode([
		"ERROR" => "Ошибка доступа."
	]);
}