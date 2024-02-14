<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Bitrix\Main\Application;
use Naturalist\Users;
use Naturalist\Certificates\Activate;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

if (!isset($_REQUEST['userId']) || $_REQUEST['userId'] == 0) {
    $params = array(
        'code' => $_REQUEST["code"],
        'login' => $_REQUEST["login"],
        'type' => $_REQUEST["type"],		
    );
    
    $users = new Users();
    $res = json_decode($users->login($params));    
    if ($res->USER_ID) {
        $cert = new Activate();
        echo json_encode($cert->resolveCode($res->USER_ID, $_REQUEST['certCode']));
    } else {
        echo json_encode([
            "ERROR_MESSAGE" => $res->ERROR
        ]);
    }
} else {
    $cert = new Activate();
    echo json_encode($cert->resolveCode($_REQUEST['userId'], $_REQUEST['certCode']));    
}