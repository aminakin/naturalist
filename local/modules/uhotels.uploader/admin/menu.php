<?php
/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CAdminMenu $this
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$menuText = Loc::getMessage("DD_BM_INNER_MENU_ADD_OBJECT_UHOTELS");
$lang = LANGUAGE_ID;

// Проверяем права доступа для вашего модуля
if ($APPLICATION->GetGroupRight("uhotels.uploader") > "D") {
    return [
        "parent_menu" => "global_menu_services", // Раздел "Сервисы"
        "sort"        => 4000,                  // Сортировка
        "text"        => $menuText,           // Текст пункта меню
        "title"       => $menuText,           // Подсказка при наведении
        "url"         => "add_object_uhotels.php?lang=" . $lang, // Ссылка на скрипт
        "icon"        => "form_menu_icon",    // Иконка пункта меню
        "page_icon"   => "form_page_icon",    // Иконка страницы
        "items_id"    => "add_uhotels_object",  // ID пункта меню
        "items"       => [],                  // Вложенные пункты меню (если есть)
    ];
}

// Возвращаем пустой массив, если права недостаточны
return [];