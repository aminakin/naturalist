<?php
/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CAdminMenu $this
 */

if($APPLICATION->GetGroupRight("seo") > "D") {
    $aMenu = [[
        "parent_menu" => "global_menu_services", // поместим в раздел "Сервис"
        "section" => "addobjectbronevik",
        "sort" => 3000,                    // сортировка пункта меню
        "url" => "add_object_bronevik_rows_list.php?lang=" . LANG,  // ссылка на пункте меню
        "text" => 'Добавить объект Bronevik',       // текст пункта меню
        "title" => 'Добавить объект Bronevik', // текст всплывающей подсказки
        "icon" => "form_menu_icon", // малая иконка
        "page_icon" => "form_page_icon", // большая иконка
        "items_id" => "add_object_bronevik",  // идентификатор ветви
        "items" => array()          // остальные уровни меню сформируем ниже.
    ]];

    return $aMenu;
}

return false;