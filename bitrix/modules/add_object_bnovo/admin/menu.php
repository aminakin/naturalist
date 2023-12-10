<?

IncludeModuleLangFile(__FILE__);

$aMenu[] = array(
    "parent_menu" => "global_menu_settings",
    "sort" => 1800,
    "text" => GetMessage("DD_BM_INNER_MENU_ADD_OBJECT_BNOVO"),
    "title" => GetMessage("DD_BM_INNER_MENU_ADD_OBJECT_BNOVO"),
    "url" => "tools_index.php?lang=".LANGUAGE_ID,
    "icon" => "util_menu_icon",
    "page_icon" => "util_page_icon",
    "items_id" => "menu_util",
    "items" => array(
        array(
            "text" => GetMessage("DD_BM_INNER_MENU_ADD_OBJECT_BNOVO"),
            "url" => "site_checker.php?lang=".LANGUAGE_ID,
            "more_url" => array(),
            "title" => GetMessage("DD_BM_INNER_MENU_ADD_OBJECT_BNOVO"),
        )
    ),
);


?>
