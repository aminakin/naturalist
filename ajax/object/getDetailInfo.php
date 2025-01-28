<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$detailId = $request->get('detailId');

if ($detailId) {
    $APPLICATION->IncludeComponent(
        "naturalist:detail.info",
        "",
        [
            'DETAIL_ID' => $detailId,
        ]
    );
}
