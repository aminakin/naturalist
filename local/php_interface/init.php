<?php

use Bitrix\Main\Loader;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("highloadblock");
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
    'Naturalist\CreateOrderPdf' => '/local/php_interface/classes/CreateOrderPdf.php',
    'Naturalist\CreateCertPdf' => '/local/php_interface/classes/CreateCertPdf.php',
    'Naturalist\Certificates\Create' => '/local/php_interface/classes/Certificates/Create.php',
    'Naturalist\Certificates\Activate' => '/local/php_interface/classes/Certificates/Activate.php',
    'Naturalist\Certificates\CatalogHelper' => '/local/php_interface/classes/Certificates/CatalogHelper.php',
    'Naturalist\Certificates\OrderHelper' => '/local/php_interface/classes/Certificates/OrderHelper.php',
    'Naturalist\Crest' => '/local/php_interface/classes/Crest.php',
    'Naturalist\Filters\EventsHandler' => '/local/php_interface/classes/Filters/EventsHandler.php',
    'Naturalist\Filters\UrlHandler' => '/local/php_interface/classes/Filters/UrlHandler.php',
    'Naturalist\Filters\AutoCreate' => '/local/php_interface/classes/Filters/AutoCreate.php',
    'Naturalist\Filters\Sitemap' => '/local/php_interface/classes/Filters/Sitemap.php',
    'Naturalist\Filters\Components' => '/local/php_interface/classes/Filters/Components.php',
    'Naturalist\BnovoDataFilesHandler' => '/local/php_interface/classes/BnovoDataFilesHandler.php',
    'Naturalist\bronevik\repository\Bronevik' => '/local/php_interface/classes/bronevik/repository/Bronevik.php',
    'Naturalist\bronevik\repository\BronevikAdapter' => '/local/php_interface/classes/bronevik/repository/BronevikAdapter.php',
    'Naturalist\bronevik\connector\HotelsConnector' => '/local/php_interface/classes/bronevik/connector/HotelsConnector.php',
    'Naturalist\bronevik\ImportHotelsBronevik' => '/local/php_interface/classes/bronevik/ImportHotelsBronevik.php',
    'Naturalist\bronevik\ImportHotelRoomsBronevik' => '/local/php_interface/classes/bronevik/ImportHotelRoomsBronevik.php',
    'Naturalist\bronevik\ImportHotelsMinPriceBronevik' => '/local/php_interface/classes/bronevik/ImportHotelsMinPriceBronevik.php',
    'Naturalist\bronevik\SearchRoomsBronevik' => '/local/php_interface/classes/bronevik/SearchRoomsBronevik.php',
    'Naturalist\bronevik\ImportHotelRoomOffersBronevik' => '/local/php_interface/classes/bronevik/ImportHotelRoomOffersBronevik.php',
    'Naturalist\bronevik\HotelRoomBronevik' => '/local/php_interface/classes/bronevik/HotelRoomBronevik.php',
    'Naturalist\bronevik\HotelRoomOfferBronevik' => '/local/php_interface/classes/bronevik/HotelRoomOfferBronevik.php',
    'Naturalist\bronevik\HotelBronevik' => '/local/php_interface/classes/bronevik/HotelBronevik.php',
    'Naturalist\bronevik\enums\RateTypeNamesEnum' => '/local/php_interface/classes/bronevik/enums/RateTypeNamesEnum.php',
    'Naturalist\bronevik\enums\RoomTypeEnum' => '/local/php_interface/classes/bronevik/enums/RoomTypeEnum.php',
    'Naturalist\bronevik\enums\PaymentRecipientEnum' => '/local/php_interface/classes/bronevik/enums/PaymentRecipientEnum.php',
    'Naturalist\bronevik\OrderCreateBronevik' => '/local/php_interface/classes/bronevik/OrderCreateBronevik.php',
    'Naturalist\bronevik\OrderCancelBronevik' => '/local/php_interface/classes/bronevik/OrderCancelBronevik.php',
    'Naturalist\bronevik\HotelRoomOfferPenaltyBronevik' => '/local/php_interface/classes/bronevik/HotelRoomOfferPenaltyBronevik.php',
    'Naturalist\bronevik\OrderCanceledPenaltyBronevik' => '/local/php_interface/classes/bronevik/OrderCanceledPenaltyBronevik.php',
    'Naturalist\bronevik\OrderBronevik' => '/local/php_interface/classes/bronevik/OrderBronevik.php',
    'Naturalist\bronevik\HotelOfferPricingCheckPriceBronevik' => '/local/php_interface/classes/bronevik/HotelOfferPricingCheckPriceBronevik.php',
    'Naturalist\bronevik\AttemptBronevik' => '/local/php_interface/classes/bronevik/AttemptBronevik.php',
    'Naturalist\Handlers\HigloadHandler' => '/local/php_interface/classes/Handlers/HigloadHandler.php',
    'Naturalist\Markdown' => '/local/php_interface/classes/Markdown.php',
    'Naturalist\Http\HttpFetchInterface' => '/local/php_interface/classes/Http/HttpFetchInterface.php',
    'Naturalist\Http\CurlHttpFetch' => '/local/php_interface/classes/Http/CurlHttpFetch.php',
    'Naturalist\Telegram\TelegramBot' => '/local/php_interface/classes/Telegram/TelegramBot.php',
    'Naturalist\Telegram\DebugBot' => '/local/php_interface/classes/Telegram/DebugBot.php',
));

// Константы
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/init_constants.php");

// Хэлперы
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/helpers.php");

// Агенты
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/agents.php");

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

if (!function_exists('custom_mail') && COption::GetOptionString("webprostor.smtp", "USE_MODULE") == "Y") {
    function custom_mail($to, $subject, $message, $additional_headers = '', $additional_parameters = '')
    {
        if (CModule::IncludeModule("webprostor.smtp")) {
            $smtp = new CWebprostorSmtp("s1");
            $result = $smtp->SendMail($to, $subject, $message, $additional_headers, $additional_parameters);

            if ($result)
                return true;
            else
                return false;
        }
    }
}
