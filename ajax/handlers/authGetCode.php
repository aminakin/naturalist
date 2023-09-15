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
	'login' => $_REQUEST["login"],
	'type' => $_REQUEST["type"],
	'email' => $_REQUEST["email"]
);

$users = new Users();
$res = $users->authGetCode($params);
echo $res;