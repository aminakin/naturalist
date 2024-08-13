<?
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Iblock\Elements\ElementGlampingsTable;

// Избранное
global $arFavourites;
$arResult["FAVOURITES"] = $arFavourites;
// Тип объекта
$hlId = 2;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLTypes = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLTypes[$arEntity["ID"]] = $arEntity;
}

$arResult["HL_TYPES"] = $arHLTypes;
$allCount = count($arResult["SECTIONS"]);
$page = $_REQUEST['page'] ?? 1;
$pageCount = ceil($allCount / $arParams["ITEMS_COUNT"]);
if ($pageCount > 1) {
    $arResult["SECTIONS"] = array_slice($arResult["SECTIONS"], ($page - 1) * $arParams["ITEMS_COUNT"],
        $arParams["ITEMS_COUNT"]);
}

// Добавляем свойство Скидка, если есть хотя бы 1 элемент со вкидкой
foreach ($arResult["SECTIONS"] as $section) {
    $arSectionIds[] = $section['ID'];
}
unset($section);

/* Отзывы */
$rsReviews = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => REVIEWS_IBLOCK_ID, "ACTIVE" => "Y"), false, false, array("ID", "PROPERTY_CAMPING_ID", "PROPERTY_RATING"));
$arReviews = array();
while ($arReview = $rsReviews->Fetch()) {
    $arReviews[$arReview["PROPERTY_CAMPING_ID_VALUE"]][$arReview["ID"]] = $arReview["PROPERTY_RATING_VALUE"];
}

$arReviewsAvg = array_map(function ($a) {
    return round(array_sum($a) / count($a), 1);
}, $arReviews);

$elements = ElementGlampingsTable::getList([
    'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
    'filter' => ['IBLOCK_SECTION_ID' => $arSectionIds],
])->fetchAll();

foreach ($elements as $element) {        
    $arElementsBySection[$element['IBLOCK_SECTION_ID']][] = $element;    
}
unset($element);

foreach ($arResult["SECTIONS"] as &$section) {
    foreach ($arElementsBySection[$section['ID']] as $element) {
        $arPrice = CCatalogProduct::GetOptimalPrice($element['ID'], 1, $USER->GetUserGroupArray(), 'N');        
        if (is_array($arPrice['DISCOUNT']) && count($arPrice['DISCOUNT'])) {
            $section['IS_DISCOUNT'] = 'Y';            
            break;
        }
    }    
}
unset($section);