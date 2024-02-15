<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

use Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

CModule::IncludeModule("iblock");
CModule::IncludeModule("form");

$name = htmlspecialchars($_REQUEST['name']);
$phone = htmlspecialchars($_REQUEST['email']);
$email = htmlspecialchars($_REQUEST['email']);

if (!empty($name) && !empty($phone)) {
    $formId = 3;
    $arValues = array(
        'form_text_12' => $name,
        'form_text_13' => $phone,
        'form_text_14' => $email,
    );
    $resultId = CFormResult::Add($formId, $arValues);

    if ($resultId) {
        $eventType = 'FORM_FILLING_SIMPLE_FORM_3';
        $eventTemplateId = 64;
        $arSend = array(
            "NAME" => $name,
            "EMAIL" => $email,
            "PHONE" => $phone
        );

        $res = CEvent::Send($eventType, SITE_ID, $arSend, 'Y', $eventTemplateId);
        if ($res) {
            echo json_encode([
                "MESSAGE" => "Ваше сообщение отправлено"
            ]);

        } else {
            echo json_encode([
                "ERROR" => "Что-то пошло не так, пожалуйста, попробуйте еще раз позже."
            ]);
        }

    } else {
        echo json_encode([
            "ERROR" => "Что-то пошло не так, пожалуйста, попробуйте еще раз позже."
        ]);
    }
} else {
    echo json_encode([
        "ERROR" => "Не заполнены обязательные поля"
    ]);
}
