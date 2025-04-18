<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Calculator\Kploader\Handlers\FormSave;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'uhotels.uploader');

/** @var $APPLICATION */
/** @var $USER */
/** @var $mid */


if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

Loader::includeModule('object.uhotels');
