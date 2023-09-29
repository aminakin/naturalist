<?

use Naturalist\Reviews;
use Naturalist\Products;
use Bitrix\Main\Web\Uri;
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

// Отзывы
$arCampingIDs = array_map(function ($a) {
    return $a["ID"];
}, $arResult["SECTIONS"]);
$arReviewsAvg = Reviews::getCampingRating($arCampingIDs);

foreach ($arResult["SECTIONS"] as &$arSection) {
    $arSection["RATING"] = $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0;
}

$arSortedSections = array();
// Выборка по наиболее близкому рейтингу
/*foreach($arResult["SECTIONS"] as $arSection) {
    if(abs($arSection["RATING"] - $arParams["SECTION_RATING"]) <= $arParams["RATING_RANGE"]) {
        $arSortedSections[$arSection["ID"]] = $arSection;
    }
}*/

/* Сортировка */
$sortBy = (!empty($_GET['sort']) && isset($_GET['sort'])) ? strtolower($_GET['sort']) : "sort";
$sortOrder = (!empty($_GET['order']) && isset($_GET['order'])) ? strtolower($_GET['order']) : "asc";
$orderReverse = (!empty($_GET['order']) && isset($_GET['order']) && $_GET['order'] == 'asc') ? "desc" : "asc";
switch ($sortBy) {
    case 'popular':
        $sort = 'UF_RESERVE_COUNT';
        break;

    default:
        $sort = 'SORT';
        break;
}

/* Ссылка */
$arUriParams = array(
    'dateFrom' => $_GET['dateFrom'],
    'dateTo' => $_GET['dateTo'],
    'guests' => $_GET['guests'],
    'children' => $_GET['children'],
    'childrenAge' => $_GET['childrenAge'],
);

$arUriParamsSort = array(
    'sort' => $sortBy,
    'order' => $sortOrder,
);

$arUriParams = array_merge($arUriParams, $arUriParamsSort);

// Выборка по наиболее близким координатам
foreach ($arResult["SECTIONS"] as $arSection) {
    if (empty($arSection["UF_COORDS"]) || empty($arParams["SECTION_COORDS"][0])) {
        continue;
    }
    //xprint($arSection["UF_COORDS"]);
    //xprint($arParams["SECTION_COORDS"]);

    $arSection["COORDS"] = explode(",", $arSection["UF_COORDS"]);

    //xprint(abs((float)$arSection["COORDS"][0] - (float)$arParams["SECTION_COORDS"][0]));
    //xprint(abs((float)$arSection["COORDS"][1] - (float)$arParams["SECTION_COORDS"][1]));

    if ((abs((float)$arSection["COORDS"][0] - (float)$arParams["SECTION_COORDS"][0]) <= (float)$arParams["COORDS_RANGE"]) && (abs((float)$arSection["COORDS"][1] - (float)$arParams["SECTION_COORDS"][1]) <= (float)$arParams["COORDS_RANGE"])) {
        $uri = new Uri($arSection["SECTION_PAGE_URL"]);
        $uri->addParams($arUriParams);
        $sectionUrl = $uri->getUri();
        $arSection["URL"] = $sectionUrl;

        $arSortedSections[$arSection["UF_EXTERNAL_ID"]] = $arSection;
        $arSectionsIds[] = $arSection["ID"];
    }
}


// Заезд, выезд, кол-во гостей
$dateFrom = $_GET['dateFrom'];
$dateTo = $_GET['dateTo'];
$guests = $_GET['guests'] ?? 2;
$children = $_GET['children'] ?? 0;
$arChildrenAge = (isset($_GET['childrenAge'])) ? explode(',' , $_GET['childrenAge']) : [];

//xprint($arSortedSections);
if (!empty($dateFrom) && !empty($dateTo) && !empty($_GET['guests'])) {
    $daysCount = abs(strtotime($dateTo) - strtotime($dateFrom)) / 86400;

    // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
    $arExternalInfo = Products::search($guests, $arChildrenAge, $dateFrom, $dateTo, false, $arSectionsIds);

    $arSortedSections = array_intersect_key($arSortedSections, $arExternalInfo);
}

if (count($arSortedSections) > $arParams["ITEMS_COUNT"]) {
    $arSortedSections = array_slice($arSortedSections, 0, $arParams["ITEMS_COUNT"]);
}
$arResult["SECTIONS"] = $arSortedSections;
$arResult["DAYS_COUNT"] = $daysCount;
$arResult["SECTIONS_EXTERNAL"] = $arExternalInfo;
$arResult["HL_TYPES"] = $arHLTypes;

// Добавляем свойство Скидка, если есть хотя бы 1 элемент со вкидкой
foreach ($arResult["SECTIONS"] as $section) {
    $arSectionIds[] = $section['ID'];
}
unset($section);

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