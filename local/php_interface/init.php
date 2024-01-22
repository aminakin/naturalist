<?php

use Bitrix\Main\Loader;

//composer autoload
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/vendor/autoload.php')) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/vendor/autoload.php');
}

// Автозагрузка классов
Loader::registerAutoLoadClasses(null, array(
    'Naturalist\Users' => '/local/php_interface/classes/Users.php',
    'Naturalist\Orders' => '/local/php_interface/classes/Order.php',
    'Naturalist\Baskets' => '/local/php_interface/classes/Basket.php',
    'Naturalist\Products' => '/local/php_interface/classes/Product.php',
    'Naturalist\Reviews' => '/local/php_interface/classes/Reviews.php',
    'Naturalist\Events' => '/local/php_interface/classes/Events.php',
    'Naturalist\Settings' => '/local/php_interface/classes/Settings.php',
    'Naturalist\Traveline' => '/local/php_interface/classes/Traveline.php',
    'Naturalist\Bnovo' => '/local/php_interface/classes/Bnovo.php',
    'Naturalist\Rest' => '/local/php_interface/classes/Rest.php',
    'Naturalist\Regions' => '/local/php_interface/classes/Regions.php',
    'Naturalist\CatalogProvider' => '/local/php_interface/classes/CatalogProvider.php',
    'Naturalist\HighLoadBlockHelper' => '/local/php_interface/classes/HighLoadBlockHelper.php',
    'Naturalist\Utils' => '/local/php_interface/classes/Utils.php',
    'Naturalist\Morpher' => '/local/php_interface/classes/Morpher.php',
    'Naturalist\CustomFunctions' => '/local/php_interface/classes/CustomFunctions.php',
    'Naturalist\PdfGenerator' => '/local/php_interface/classes/PdfGenerator.php',
    'Naturalist\PrepareOrderData' => '/local/php_interface/classes/PrepareOrderData.php',
));

// Константы
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/init_constants.php");

// Хэлперы
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/helpers.php");

// Библиотеки
//require_once($_SERVER["DOCUMENT_ROOT"]."/local/vendor/autoload.php");

// События
Naturalist\Events::bindEvents();

AddEventHandler("main", "OnBuildGlobalMenu", "menuAdminNewItem");
function menuAdminNewItem(&$adminMenu, &$moduleMenu)
{
    $moduleMenu[] = array(
        "parent_menu" => "global_menu_services", // поместим в раздел "Сервис"
        "section" => "add_object_bnovo",
        "sort" => 2000,                    // сортировка пункта меню
        "url" => "add_object_bnovo.php?lang=" . LANG,  // ссылка на пункте меню
        "text" => 'Добавить объект Bnovo',       // текст пункта меню
        "title" => 'Добавить объект Bnovo', // текст всплывающей подсказки
        "icon" => "form_menu_icon", // малая иконка
        "page_icon" => "form_page_icon", // большая иконка
        "items_id" => "add_object_bnovo",  // идентификатор ветви
        "items" => array()          // остальные уровни меню сформируем ниже.
    );
}