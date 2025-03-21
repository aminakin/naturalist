<?php

use Bitrix\Main\Application;
use Naturalist\Bnovo;

set_time_limit(0);
if (!$_SERVER['DOCUMENT_ROOT']) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__)."/../../");
}
include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

$bnovo = new Bnovo();
$bnovo->getNearPrices();

