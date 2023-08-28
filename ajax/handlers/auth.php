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

$params = array(
	'code' => $_REQUEST["code"],
	'login' => $_REQUEST["login"],
	'type' => $_REQUEST["type"],
	'page' => $_REQUEST["page"],
);

$users = new Users();
$res = $users->login($params);
echo $res;