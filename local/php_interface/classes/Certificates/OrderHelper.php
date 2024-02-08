<?php

namespace Naturalist\Certificates;

use Bitrix\Main\Loader;
use Naturalist\HighLoadBlockHelper;

/**
 * Работа с заказом сертификата.
 */

class OrderHelper
{
    public function __construct()
    {
        Loader::includeModule("sale");
    }
}