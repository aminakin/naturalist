<?php

use Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$current_catalog_page = $request->get("page");
$catalog_showen_items = $request->get("items");

if ($current_catalog_page != '' && $catalog_showen_items != '') {
    $session = Application::getInstance()->getSession();
    $session->set('current_catalog_page', $current_catalog_page);
    $session->set('catalog_showen_items', $catalog_showen_items);

    echo json_encode([
        "MESSAGE" => "Значения локального хранилища установлены",
        "STATUS" => "SUCCESS"
    ]);
} else {
    echo json_encode([
        "MESSAGE" => "Значения локального хранилища не установлены",
        "STATUS" => "ERROR"
    ]);
}