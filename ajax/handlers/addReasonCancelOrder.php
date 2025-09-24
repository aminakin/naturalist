<?php

use \Bitrix\Main\Loader;
use Bitrix\Main\Application;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	  include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
}

if (!Loader::includeModule('iblock')) {
    echo json_encode([
		    'ERROR' => 'Не подключен модуль Iblock'
	  ]);
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$reason = $request->getPost('reason');
$answer = $request->getPost('answer');
$orderId = $request->getPost('orderId');

if (!$orderId) {
    echo json_encode([
		    'ERROR' => 'Не выбрана причина отмены'
	  ]);
}

try {
    $elementFields = [
        'IBLOCK_ID' => REASONS_CANCEL_ORDER,
        'NAME' => 'Отмена заказа с ID ' . $orderId,
        'IBLOCK_SECTION_ID' => false,
        'CODE' => 'reason_' . $orderId,
        'PROPERTY_VALUES' => [
            'REASONS' => $reason,
            'ANSWER' => $answer ?: ''
        ]
    ];
    
    $el = new \CIBlockElement;
    $elementId = $el->Add($elementFields);

    if ($elementId) {
        echo json_encode([
            'SUCCESS' => 'Y'
        ]);
    } else {
        echo json_encode([
            'ERROR' => 'Ошибка добавления результата ' + $el->LAST_ERROR
        ]);
    }
} catch (Exception $e) {
  echo json_encode([
		  'ERROR' => 'Ошибка ' . $e->getMessage()
	]);
}