<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Naturalist\CreateOrderPdf;

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

if ($request->isPost()) {
    $PDF = new CreateOrderPdf();
    $arPostValues = $request->getPostList()->getValues();
    $orderId = $arPostValues['orderId'];
    if ($orderId) {
        $result = $PDF->getPdfLink($orderId);
        echo $result;
    } else {
        echo json_encode([
            "ERROR" => "Не указан номер заказа!"
        ]);
    }    
}