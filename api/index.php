<?php
use Naturalist\Rest;

define("STOP_STATISTICS", true);
define("NO_AGENT_CHECK", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_REQUEST['resource'];

$api = new Rest();
$arOutput = array();

if($method == 'POST' && $resource == 'auth') {
    $inputJSON = file_get_contents('php://input');
    $params    = json_decode($inputJSON, true);

    $clientId     = $params['client_id'];
    $clientSecret = $params['client_secret'];

    $arOutput = $api->getToken($clientId, $clientSecret);
    $responseCode = (!empty($arOutput['code'])) ? 401 : 200;
    unset($arOutput['code']);

} else {
    $arHeaders = getallheaders();
    list($authType, $token) = explode(' ', $arHeaders["Authorization"]);

    $validation = $api->login($token);
    if($validation === TRUE) {
        $isExist = false;

        if(isset($resource) && !empty($resource)) {
            switch($method) {
                case 'GET':
                    switch($resource) {
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
                    switch($resource) {
                        case 'catalog':
                            $inputJSON = file_get_contents('php://input');
                            $params    = json_decode($inputJSON, true);

                            $arOutput = $api->updateCatalog($params);
                            $responseCode = $arOutput['code'];
                            //unset($arOutput['code']);

                            $isExist = true;
                            break;

                        case 'prices':
                            $inputJSON = file_get_contents('php://input');
                            $params    = json_decode($inputJSON, true);

                            $arOutput = $api->updatePrices($params);
                            $responseCode = $arOutput['code'];
                            //unset($arOutput['code']);

                            $isExist = true;
                            break;
                    }

                    break;
            }

            if(!$isExist) {
                $arOutput     = array('code' => 404, 'error' => 'Ресурс не найден.');
                $responseCode = 404;
            }

        } else {
            $arOutput     = array('code' => 400, 'error' => 'Ошибка в запросе.');
            $responseCode = 400;
        }

    } else {
        $arOutput = $validation;
        $responseCode = 401;
    }
}

header('Content-Type: application/json; charset=utf-8');

switch ($responseCode) {
    case 200: $responseText = 'OK'; break;
    case 400: $responseText = 'Bad Request'; break;
    case 401: $responseText = 'Unauthorized'; break;
    case 402: $responseText = 'Payment Required'; break;
    case 403: $responseText = 'Forbidden'; break;
    case 404: $responseText = 'Not Found'; break;
    case 405: $responseText = 'Method Not Allowed'; break;
    case 406: $responseText = 'Not Acceptable'; break;
}
header("HTTP/1.1 ".$responseCode." ".$responseText);

$APPLICATION->RestartBuffer();
echo json_encode($arOutput, JSON_UNESCAPED_UNICODE);
?>