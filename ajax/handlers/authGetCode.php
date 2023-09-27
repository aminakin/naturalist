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

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$params = [
	'login' => $request->get("login"),
	'type' => $request->get("type"),
	'email' => $request->get("email"),
	'name' => $request->get("name"),
	'last_name' => $request->get("last_name"),
];

$users = new Users();
$res = $users->authGetCode($params);
echo $res;