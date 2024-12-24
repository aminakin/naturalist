<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Naturalist\Certificates\CatalogHelper;

// Картинки сертификатов
$certificates = new CatalogHelper;
$arResult['VARIANTS'] = $certificates->hlElVariantsValues;
