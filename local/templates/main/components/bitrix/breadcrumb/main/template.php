<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$str = '';


if (!empty($arResult)) {
    $itemList = [];
    foreach ($arResult as $index => $item) {
        if (!empty($item['LINK'])) {
            $itemList[] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "name" => $item['TITLE'],
                "item" => "https://" . $_SERVER["HTTP_HOST"] . $item["LINK"]
            ];
        }
    }

    $breadcrumbSchema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $itemList
    ];

    $str .= '<script type="application/ld+json">' . 
        json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . 
        '</script>';
}


foreach ($arResult as $key => $arItem) {
    $isLast = ($key == count($arResult) - 1);
    $str .= '<li class="list__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    
    if (!$isLast && !empty($arItem['LINK'])) {
        $str .= '<a class="list__link" href="' . htmlspecialchars($arItem["LINK"]) . '" itemprop="item">';
        $str .= '<span itemprop="name">' . htmlspecialchars($arItem["TITLE"]) . '</span>';
        $str .= '</a>';
    } else {
        // Последний элемент — просто текст
        $str .= '<span class="list__link" itemprop="name">' . htmlspecialchars($arItem["TITLE"]) . '</span>';
    }

    $str .= '<meta itemprop="position" content="' . ($key + 1) . '" />';
    $str .= '</li>';
}




return $str;
