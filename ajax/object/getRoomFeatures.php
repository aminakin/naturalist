<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$roomId = $request->get('roomId');

if ($roomId) {
    $APPLICATION->IncludeComponent(
        "naturalist:object.info",
        "",
        [
            'ROOM_ID' => $roomId,
        ]
    );
}
