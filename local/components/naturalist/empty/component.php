<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$catalogFilterData = [];
if ($arParams['arHLTypes']){
    foreach ($arParams['arHLTypes'] as $arType){
        if ($arParams['arFilterTypes'] && in_array($arType["ID"], $arParams['arFilterTypes'])){
            $catalogFilterData[] = [
                'ID' =>  $arType["ID"],
                'NAME' =>  $arType["UF_NAME"],
                'TYPE' => 'types'
            ];
        }
    }
}
if ($arParams['restVariants']){
    foreach ($arParams['restVariants'] as $restVariant){
        if ($arParams['arFilterRestVariants'] && in_array($restVariant["ID"], $arParams['arFilterRestVariants'])){
            $catalogFilterData[] = [
                'ID' =>  $restVariant["ID"],
                'NAME' =>  $restVariant["UF_NAME"],
                'TYPE' => 'restvariants'
            ];
        }
    }
}
if ($arParams['water']){
    foreach ($arParams['water'] as $oneWater){
        if ($arParams['arFilterWater'] && in_array($oneWater["ID"], $arParams['arFilterWater'])){
            $catalogFilterData[] = [
                'ID' =>  $oneWater["ID"],
                'NAME' =>  $oneWater["UF_NAME"],
                'TYPE' => 'water'
            ];
        }
    }
}
if ($arParams['houseTypes']){
    foreach ($arParams['houseTypes'] as $houseType){
        if ($arParams['arFilterHouseTypes'] && in_array($houseType["ID"], $arParams['arFilterHouseTypes'])){
            $catalogFilterData[] = [
                'ID' =>  $houseType["ID"],
                'NAME' =>  $houseType["UF_NAME"],
                'TYPE' => 'housetypes'
            ];
        }
    }
}
if ($arParams['arServices']){
    foreach ($arParams['arServices'] as $arService){
        if ($arParams['arFilterServices'] && in_array($arService["ID"], $arParams['arFilterServices'])){
            $catalogFilterData[] = [
                'ID' =>  $arService["ID"],
                'NAME' =>  $arService["UF_NAME"],
                'TYPE' => 'services'
            ];
        }
    }
}
if ($arParams['arHLFood']){
    foreach ($arParams['arHLFood'] as $arFoodItem){
        if ($arParams['arFilterFood'] && in_array($arFoodItem["ID"], $arParams['arFilterFood'])){
            $catalogFilterData[] = [
                'ID' =>  $arFoodItem["ID"],
                'NAME' =>  $arFoodItem["UF_NAME"],
                'TYPE' => 'food'
            ];
        }
    }
}
if ($arParams['objectComforts']){
    foreach ($arParams['objectComforts'] as $objectComfort){
        if ($arParams['arFilterObjectComforts'] && in_array($objectComfort["ID"], $arParams['arFilterObjectComforts'])){
            $catalogFilterData[] = [
                'ID' => $objectComfort["ID"],
                'NAME' => $objectComfort["UF_NAME"],
                'TYPE' => 'objectcomforts'
            ];
        }
    }
}
if ($arParams['arHLFeatures']){
    foreach ($arParams['arHLFeatures'] as $arFeature){
        if ($arParams['arFilterFeatures'] && in_array($arFeature["ID"], $arParams['arFilterFeatures'])){
            $catalogFilterData[] = [
                'ID' =>  $arFeature["ID"],
                'NAME' =>  $arFeature["UF_NAME"],
                'TYPE' => 'feature'
            ];
        }
    }
}
$this->IncludeComponentTemplate();

return $catalogFilterData;