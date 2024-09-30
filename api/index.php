<?php

use Naturalist\Rest;
use Bitrix\Main\Diag\Debug;

define("STOP_STATISTICS", true);
define("NO_AGENT_CHECK", true);
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_REQUEST['resource'];

//Debug::writeToFile($_REQUEST, 'BNOVO_REQUEST_' . date('Y-m-d H:i:s'), '__BNOVO_REQUEST.log');

$api = new Rest();
$arOutput = array();

$inputJSON = file_get_contents('php://input');
$params    = json_decode($inputJSON, true);
$filePath = $api->setCurrentData($params);
$arSend['FILE'] = $filePath;

if ($method == 'POST' && $resource == 'auth') {
    $clientId     = $params['client_id'];
    $clientSecret = $params['client_secret'];

    $arOutput = $api->getToken($clientId, $clientSecret);
    $responseCode = (!empty($arOutput['code'])) ? 401 : 200;
    unset($arOutput['code']);
} elseif ($resource == 'prices') {
    $params    = json_decode($inputJSON, true);
    $arOutput = $api->updatePrices($params, $filePath);
    $responseCode = $arOutput['code'];
} else {
    $arHeaders = getallheaders();
    list($authType, $token) = explode(' ', $arHeaders["Authorization"]);

    $validation = $api->login($token);
    if ($validation === TRUE) {
        $isExist = false;

        if (isset($resource) && !empty($resource)) {
            switch ($method) {
                case 'GET':
                    switch ($resource) {
                        case 'catalog':
                            $hotelId = $_GET['hotel_id'];
                            $arOutput = $api->getCatalog($hotelId);
                            $responseCode = $arOutput['code'];
                            //unset($arOutput['code']);

                            $isExist = true;
                            break;
                    }

                    break;

                case 'POST':
                    switch ($resource) {
                        case 'catalog':
                            $arOutput = $api->updateCatalog($params);
                            $responseCode = $arOutput['code'];
                            //unset($arOutput['code']);

                            $isExist = true;
                            break;

                        case 'prices':
                            $hotelId = $params["hotel_id"];
                            $externalId = $params["account_id"];

                            $arSend['HOTEL_ID'] = $hotelId;
                            $arSend['EXT_ID'] = $externalId;

                            $arSectionHotel = CIBlockSection::GetList(
                                false,
                                array(
                                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                                    "ID" => $hotelId,
                                    "UF_EXTERNAL_SERVICE" => "2",
                                ),
                                false,
                                array("IBLOCK_ID", "ID"),
                                false
                            )->Fetch();

                            if (empty($arSectionHotel["ID"])) {
                                $arSend['MESSAGE'] = 'Объект не найден';
                                $api->sendError($arSend);

                                $responseCode = 403;
                                $arOutput = array('code' => 403, 'error' => 'Объект не найден');
                            } else {
                                $arRooms = $params["data"]["rooms"];
                                $arPrices = $params["data"]["prices"];
                                if (!$arPrices && !$arRooms) {
                                    if (!$arPrices) {
                                        $arSend['MESSAGE'] = 'Параметр prices не был передан.';
                                        $api->sendError($arSend);

                                        $responseCode = 404;
                                        $arOutput = array('code' => 404, 'error' => 'Параметр prices не был передан.');
                                    } else {
                                        $arSend['MESSAGE'] = 'Параметр rooms не был передан.';
                                        $api->sendError($arSend);

                                        $responseCode = 404;
                                        $arOutput = array('code' => 404, 'error' => 'Параметр rooms не был передан.');
                                    }
                                } else {
                                    exec("php " . $_SERVER["DOCUMENT_ROOT"] . "/api/index.php prices " . $filePath . " > /dev/null 2>&1 &");

                                    $responseCode = 200;
                                    $arOutput = array('code' => 200, 'message' => 'Ok');
                                }
                            }

                            $isExist = true;
                            break;
                    }

                    break;
            }

            if (!$isExist) {
                $arSend['MESSAGE'] = 'Ресурс не найден.';
                $api->sendError($arSend);
                $arOutput     = array('code' => 404, 'error' => 'Ресурс не найден.');
                $responseCode = 404;
            }
        } else {
            $arSend['MESSAGE'] = 'Ошибка в запросе.';
            $api->sendError($arSend);
            $arOutput     = array('code' => 400, 'error' => 'Ошибка в запросе.');
            $responseCode = 400;
        }
    } else {
        $arOutput = $validation;
        //$arSend['MESSAGE'] = $arOutput;
        //$api->sendError($arSend);
        $responseCode = 401;
    }
}

header('Content-Type: application/json; charset=utf-8');

switch ($responseCode) {
    case 200:
        $responseText = 'OK';
        break;
    case 400:
        $responseText = 'Bad Request';
        break;
    case 401:
        $responseText = 'Unauthorized';
        break;
    case 402:
        $responseText = 'Payment Required';
        break;
    case 403:
        $responseText = 'Forbidden';
        break;
    case 404:
        $responseText = 'Not Found';
        break;
    case 405:
        $responseText = 'Method Not Allowed';
        break;
    case 406:
        $responseText = 'Not Acceptable';
        break;
}
header("HTTP/1.1 " . $responseCode . " " . $responseText);

$APPLICATION->RestartBuffer();
echo json_encode($arOutput, JSON_UNESCAPED_UNICODE);
die();
