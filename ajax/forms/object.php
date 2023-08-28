<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */
use Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

CModule::IncludeModule("iblock");
CModule::IncludeModule("form");

$name    = htmlspecialchars($_REQUEST['name']);
$site    = htmlspecialchars($_REQUEST['site']);
$module  = htmlspecialchars($_REQUEST['module']);
$fio     = htmlspecialchars($_REQUEST['fio']);
$email   = htmlspecialchars($_REQUEST['email']);
$phone   = htmlspecialchars($_REQUEST['phone']);
$message = htmlspecialchars($_REQUEST['message']) ?? '';

if(!empty($name) && !empty($site) && !empty($module) && !empty($fio) && !empty($email) && !empty($phone)) {
    $formId = 2;
    $arValues = array(
        'form_text_4' => $name,
        'form_text_5' => $site,
        'form_text_6' => $module,
        'form_text_8' => $fio,
        'form_email_9' => $email,
        'form_text_10' => $phone,
        'form_textarea_11' => $message,
    );
    $resultId = CFormResult::Add($formId, $arValues);

    if($resultId) {
        $eventType = 'OBJECT_FORM';
        $eventTemplateId = 57;
        $arSend = array(
            "NAME"    => $name,
            "SITE"    => $site,
            "MODULE"  => $module,
            "FIO"     => $fio,
            "EMAIL"   => $email,
            "PHONE"   => $phone,
            "MESSAGE" => $message
        );

        $res = CEvent::Send($eventType, SITE_ID, $arSend, 'Y', $eventTemplateId);
        if($res) {
            echo json_encode([
                "MESSAGE" => "Ваше сообщение успешно отправлено."
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
