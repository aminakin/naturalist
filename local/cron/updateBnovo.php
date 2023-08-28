<?
use Bitrix\Main\Application;
use Naturalist\Bnovo;

set_time_limit(600);
if (!$_SERVER['DOCUMENT_ROOT']) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../../");
}
include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

$bnovo = new Bnovo();
$bnovo->updatePublicObject('16acbe2b-f3fc-49b8-8a77-b906cca29e72');
//$bnovo->update();