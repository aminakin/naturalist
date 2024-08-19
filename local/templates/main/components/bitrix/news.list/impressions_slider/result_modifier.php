<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

foreach ($arResult['ITEMS'] as $key => &$item) {
    if ($item['IBLOCK_SECTION_ID'] != '') {
        unset($arResult['ITEMS'][$key]);
    }
}