<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

foreach ($arResult['ITEMS'] as $key => &$item) {
    if ($item['IBLOCK_SECTION_ID'] != '') {
        unset($arResult['ITEMS'][$key]);
    }

    $item['MOBILE_IMG'] = CFile::ResizeImageGet(
        $item["PREVIEW_PICTURE"]["ID"],
        array('width' => 686, 'height' => 484),
        BX_RESIZE_IMAGE_EXACT,
        true,
        false,
        false,
        80
    )['src'];

    $item['DESKTOP_IMG'] = CFile::ResizeImageGet(
        $item['FIELDS']["DETAIL_PICTURE"]["ID"],
        array('width' => 1200, 'height' => 360),
        BX_RESIZE_IMAGE_EXACT,
        true,
        false,
        false,
        80
    )['src'];
}
