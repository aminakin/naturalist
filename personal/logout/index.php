<?
use Bitrix\Main\Application;
use Naturalist\Users;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

global $userId;
if($userId > 0) {
    Users::logout();
}