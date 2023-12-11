<?
use Bitrix\Main\Application;
use Naturalist\Bnovo;

set_time_limit(600);
if (!$_SERVER['DOCUMENT_ROOT']) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../../");
}
include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

$bnovo = new Bnovo();
$bnovo->updatePublicObject('043a384c-3106-4a9a-838b-3e013530bf93');