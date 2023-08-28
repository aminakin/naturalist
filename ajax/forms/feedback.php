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
$email = htmlspecialchars($_REQUEST['email']);
$message = htmlspecialchars($_REQUEST['message']);

if (!empty($name) && !empty($email)) {
    $formId = 1;
    $arValues = array(
        'form_text_1' => $name,
        'form_text_2' => $email,
        'form_textarea_3' => $message,
    );
    $resultId = CFormResult::Add($formId, $arValues);

    if ($resultId) {
        $eventType = 'FEEDBACK_FORM';
        $eventTemplateId = 7;
        $arSend = array(
            "NAME" => $name,
            "EMAIL" => $email,
            "MESSAGE" => $message
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
