<?

use Naturalist\Reviews;
use Naturalist\Products;
use Naturalist\Utils;
use Naturalist\Regions;
use Bitrix\Main\Web\Uri;
use Bitrix\Highloadblock\HighloadBlockTable;

// Избранное
global $arFavourites;
$arResult["FAVOURITES"] = $arFavourites;

// Тип объекта
$entityClass = HighloadBlockTable::compileEntity(TYPES_HL_ENTITY)->getDataClass();
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
$arResult['arReviewsAvg'] = $arReviewsAvg;

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
foreach ($arResult["SECTIONS"] as &$arSection) {
    if (empty($arSection["UF_COORDS"]) || empty($arParams["SECTION_COORDS"][0])) {
        continue;
    }

    $arSection["COORDS"] = explode(",", $arSection["UF_COORDS"]);

    $searchedRegionData = Regions::getRegionById($arParams['AR_SECTION']['UF_REGION'] ?? false);
    if ($searchedRegionData['CENTER_UF_REGION']) {
        $arSection['DISCTANCE'] = Utils::calculateTheDistance(
            (float)$arParams['SECTION_COORDS'][0],
            (float)$arParams['SECTION_COORDS'][1],
            (float)$arSection['COORDS'][0],
            (float)$arSection['COORDS'][1]
        );

        $arSection['DISCTANCE_TO_REGION'] = $searchedRegionData['UF_CENTER_NAME_RU'];
    }

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
if (isset($_GET['childrenAge'])) {
    if (is_array($_GET['childrenAge'])) {
        $arChildrenAge = $_GET['childrenAge'];
    } else {
        $arChildrenAge = explode(',', $_GET['childrenAge']);
    }
} else {
    $arChildrenAge = [];
}

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

/** получение сезона из ИБ */
if (Cmodule::IncludeModule('asd.iblock')) {
    $arFields = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
}

foreach ($arResult["SECTIONS"] as &$arItem) {
    $arDataFullGallery = [];
    foreach ($arFields['UF_SEASON'] as $season) {
        if ($season == 'Лето') {
            if ($arItem["UF_PHOTOS"]) {
                foreach ($arItem["UF_PHOTOS"] as $photoId) {
                    $imageOriginal = CFile::GetFileArray($photoId);
                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                    $arItem["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                $arItem["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        } elseif ($season == 'Зима') {
            if ($arItem["UF_WINTER_PHOTOS"]) {
                foreach ($arItem["UF_WINTER_PHOTOS"] as $photoId) {
                    $imageOriginal = CFile::GetFileArray($photoId);
                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                    $arItem["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                if ($arItem["UF_PHOTOS"]) {
                    foreach ($arItem["UF_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $arItem["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                    }
                } else {
                    $arItem["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                }
            }
        } elseif ($season == 'Осень+Весна') {
            if ($arItem["UF_MIDSEASON_PHOTOS"]) {
                foreach ($arItem["UF_MIDSEASON_PHOTOS"] as $photoId) {
                    $imageOriginal = CFile::GetFileArray($photoId);
                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                    $arItem["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                if ($arItem["UF_PHOTOS"]) {
                    foreach ($arItem["UF_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $arItem["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                    }
                } else {
                    $arItem["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                }
            }
        }
    }
    $arItem["FULL_GALLERY"] = implode(",", $arDataFullGallery);
    unset($arItem["UF_PHOTOS"]);
}
