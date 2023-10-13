<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Highloadblock;
use Bitrix\Main\Entity;
use Naturalist\Regions;


CModule::IncludeModule('iblock');

if (!Main\Loader::includeModule('highloadblock')) {
    throw new Main\LoaderException(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
}

$request = Application::getInstance()->getContext()->getRequest();

/**
 * Sorting item flats
 */


if ($request->get('text') != null) {
    $requestName = urldecode($request->get('text'));
}

if ($requestName) {

    function array_unique_key($array, $key)
    {
        $tmp = $key_array = array();
        $i = 0;

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $tmp[$i] = $val;
            }
            $i++;
        }
        return $tmp;
    }

    $arFilterArea = [
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y"
    ];

    $arFilterStreet = [
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y"
    ];

    $arFilterObject = [
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y"
    ];

    $arCities = $arRegions = [];
    if ($requestName != null) {
        $requestSearchName = (is_array($requestName)) ? $requestName : explode(',', $requestName);

        if (is_array($requestSearchName) && count($requestSearchName) > 0) {

            $arCities = Regions::getCityByName($requestSearchName);
            $arRegions = Regions::getRegionByName($requestSearchName);
            $arCityRegions =  Regions::getRegionByCity($requestSearchName); /* регионы этого города */

//            $arFilterArea["%UF_REGION_NAME"] = $requestSearchName;
            $arFilterStreet["%UF_ADDRESS"] = $requestSearchName;
            $arFilterObject["%NAME"] = $requestSearchName;



        }
    }

    $arAreas = [
        'type' => 'Регион',
        'id' => 'area',
        'list' => [],
    ];

    $arStreets = [
        'type' => 'Объекты размещения',
        'id' => 'id',
        'list' => [],
    ];

    /*$arObjects = [
        'type' => 'Объект',
        'id' => 'object',
        'list' => [],
    ];*/

//    $resAreas = CIBlockSection::GetList(
//        array(),
//        $arFilterArea,
//        false,
//        array(
//            "ID",
//            "IBLOCK_ID",
//            "CODE",
//            "NAME",
//            "PICTURE",
//            "UF_*"
//        ),
//        false
//    );
//
//    while ($arArea = $resAreas->GetNext()) {
//        $area = $arArea['UF_REGION_NAME'];
//        if (in_array($area, $arAreas['list'])) {
//            continue;
//        }
//        $arAreas['list'][] = [
//            'id' => $area,
//            'title' => $area
//        ];
//    }
//    $arAreas['list'] = array_unique($arAreas['list']);

    foreach ($arCities as $arCity) {
        $arAreas['list'][] = [
            'id' => $arCity['UF_NAME'],
            'title' => $arCity['UF_NAME'],
            'footnote' => $arCity['REGION_UF_NAME']
        ];
    }

    foreach ($arRegions as $arRegion) {
        $arAreas['list'][] = [
            'id' => $arRegion['UF_NAME'],
            'title' => $arRegion['UF_NAME']
        ];
    }


    /* поиск по адресу */
    $resStreets = CIBlockSection::GetList(
        false,
        $arFilterStreet,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "CODE",
            "NAME",
            "PICTURE",
            "UF_*"
        ),
        false
    );

    while ($arStreet = $resStreets->GetNext()) {
        $arStreets['list'][] = [
            'id' => $arStreet['ID'],
            'title' => $arStreet['NAME'],
            'footnote' => $arStreet['UF_ADDRESS']
        ];
    }

    //находим объекты
    $resObjects = CIBlockSection::GetList(
        false,
        $arFilterObject,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "CODE",
            "NAME",
            "PICTURE",
            "UF_*"
        ),
        false
    );

    while ($arObject = $resObjects->GetNext()) {
        $arStreets['list'][] = [
            'id' => $arObject['ID'],
            'title' => $arObject['NAME'],
            'footnote' => $arObject['UF_ADDRESS']
        ];
    }

    $arStreets['list'] = array_unique_key($arStreets['list'], 'title');

    if ($arAreas['list']) {
        $arReturn[] = $arAreas;
    }
    if ($arStreets['list']) {
        $arReturn[] = $arStreets;
    }
    /*if ($arObjects['list']) {
        $arReturn[] = $arObjects;
    }*/

    if(isset($arReturn) && !empty($arReturn)){
        echo $encode = json_encode($arReturn, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }else {
        echo $encode = json_encode(["messageType" => "error", "messageText" => "Объекты или регион не найдены"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
} else {
    //show favorite
    $arRegions = Regions::getFavoriteRegions();

    $arAreas = [
        'type' => 'Регион',
        'id' => 'area',
        'list' => [],
    ];


    foreach ($arRegions as $arRegion) {
        $arAreas['list'][] = [
            'id' => $arRegion['UF_NAME'],
            'title' => $arRegion['UF_NAME']
        ];
    }

    if ($arAreas['list']) {
        $arReturn[] = $arAreas;
    }

    if(isset($arReturn) && !empty($arReturn)){
        echo $encode = json_encode($arReturn, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }else {
        echo $encode = json_encode(["messageType" => "error", "messageText" => "Объекты или регион не найдены"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
