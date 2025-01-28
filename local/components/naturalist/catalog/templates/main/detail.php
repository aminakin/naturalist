<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Naturalist\Users;
use Naturalist\Products;
use Naturalist\Reviews;

global $arUser, $userId, $isAuthorized;

$APPLICATION->SetAdditionalCSS('https://unpkg.com/zuck.js/dist/zuck.css');
$APPLICATION->AddHeadScript('https://unpkg.com/zuck.js/dist/zuck.js');

$request = Application::getInstance()->getContext()->getRequest();
$isAjax  = $request->isAjaxRequest();

/* Избранное (список ID) */
$arFavourites = Users::getFavourites();

/* Текущий раздел */

$arSection = CIBlockSection::GetList(false, array("ACTIVE" => "Y", "IBLOCK_ID" => CATALOG_IBLOCK_ID, "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"]), false, array("IBLOCK_ID", "ID", "NAME", "CODE", "DESCRIPTION", "SECTION_PAGE_URL", "UF_*"), false)->GetNext();
if (!$arSection) {
    include($_SERVER["DOCUMENT_ROOT"] . '/404.php');
    exit;
}

$arEnum = CUserFieldEnum::GetList(array(), array("CODE" => "UF_EXTERNAL_SERVICE", "ID" => $arSection["UF_EXTERNAL_SERVICE"]))->GetNext();
$arSection["UF_EXTERNAL_SERVICE"] = $arEnum['XML_ID'];

/** получение сезона из ИБ */
if (Cmodule::IncludeModule('asd.iblock')) {
    $arFields = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
}

foreach ($arFields['UF_SEASON'] as $season) {
    if ($season == 'Лето') {
        if ($arSection["UF_PHOTOS"]) {
            foreach ($arSection["UF_PHOTOS"] as $photoId) {
                $imageOriginal = CFile::GetFileArray($photoId);
                $arSection["PICTURES"][] = [
                    'src' => CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true)['src'],
                    'big' => CFile::GetFileArray($photoId)['SRC']
                ];
            }
        } else {
            $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
        }
        if ($arSection['UF_STORIES_SUMMER'] && $arSection['UF_STORIES_PREVIEW_SUMMER']) {
            foreach ($arSection['UF_STORIES_SUMMER'] as $key => $story) {
                $arSection['STORIES'][] =
                    [
                        'name' => $arSection['NAME'],
                        'video' => CFile::GetPath($story),
                        'preview' => CFile::GetPath($arSection['UF_STORIES_PREVIEW_SUMMER'][$key]),
                    ];
            }
        }
    } elseif ($season == 'Зима') {
        if ($arSection["UF_WINTER_PHOTOS"]) {
            foreach ($arSection["UF_WINTER_PHOTOS"] as $photoId) {
                $arSection["PICTURES"][] = [
                    'src' => CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true)['src'],
                    'big' => CFile::GetFileArray($photoId)['SRC']
                ];
            }
        } else {
            if ($arSection["UF_PHOTOS"]) {
                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                    $arSection["PICTURES"][] = [
                        'src' => CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true)['src'],
                        'big' => CFile::GetFileArray($photoId)['SRC']
                    ];
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
        if ($arSection['UF_STORIES_WINTER'] && $arSection['UF_STORIES_PREVIEW_WINTER']) {
            foreach ($arSection['UF_STORIES_WINTER'] as $key => $story) {
                $arSection['STORIES'][] =
                    [
                        'name' => $arSection['NAME'],
                        'video' => CFile::GetPath($story),
                        'preview' => CFile::GetPath($arSection['UF_STORIES_PREVIEW_WINTER'][$key]),
                    ];
            }
        }
    } elseif ($season == 'Осень+Весна') {
        if ($arSection["UF_MIDSEASON_PHOTOS"]) {
            foreach ($arSection["UF_MIDSEASON_PHOTOS"] as $photoId) {
                $arSection["PICTURES"][] = [
                    'src' => CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true)['src'],
                    'big' => CFile::GetFileArray($photoId)['SRC']
                ];
            }
        } else {
            if ($arSection["UF_PHOTOS"]) {
                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                    $arSection["PICTURES"][] = [
                        'src' => CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true)['src'],
                        'big' => CFile::GetFileArray($photoId)['SRC']
                    ];
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
        if ($arSection['UF_STORIES_MIDSEASON'] && $arSection['UF_STORIES_PREVIEW_MIDSEASON']) {
            foreach ($arSection['UF_STORIES_MIDSEASON'] as $key => $story) {
                $arSection['STORIES'][] =
                    [
                        'name' => $arSection['NAME'],
                        'video' => CFile::GetPath($story),
                        'preview' => CFile::GetPath($arSection['UF_STORIES_PREVIEW_MIDSEASON'][$key]),
                    ];
            }
        }
    }
}
unset($arSection["UF_PHOTOS"]);

if ($arSection["UF_COORDS"]) {
    /* Метеоданные */
    $coords = $arSection["UF_COORDS"];
    $arSection["COORDS"] = explode(',', $arSection["UF_COORDS"]);
}
/** Услуги */
if ($arSection["UF_SERVICES"]) {
    $rsServices = CIBlockElement::GetList(false, array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "!IBLOCK_SECTION_ID" => false, "ID" => $arSection["UF_SERVICES"]), false, false, array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME"));
    $arServices = array();
    while ($arService = $rsServices->Fetch()) {
        $arServices[] = $arService;
    }
}

if (!$arSection) {
    include($_SERVER["DOCUMENT_ROOT"] . '/404.php');
    exit;
}
$APPLICATION->SetTitle($arSection["NAME"]);

/* Склонения */
$guestsDeclension = new Declension('гость', 'гостя', 'гостей');
$childrenDeclension = new Declension('ребенок', 'детей', 'детей');
$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');
$daysDeclension = new Declension('ночь', 'ночи', 'ночей');
$roomsDeclension = new Declension('комната', 'комнаты', 'комнат');
$bedsDeclension = new Declension('спальное место', 'спальных места', 'спальных мест');

/* Отзывы */
$reviewsSortType = (!empty($_GET['sort']) && isset($_GET['sort'])) ? strtolower($_GET['sort']) : "date";
switch ($reviewsSortType) {
    case 'date':
        $arReviewsSort = array("ACTIVE_FROM" => "DESC");
        break;

    case 'negative':
        $arReviewsSort = array("PROPERTY_RATING" => "ASC");
        break;

    case 'positive':
        $arReviewsSort = array("PROPERTY_RATING" => "DESC");
        break;
}
$rsReviews = CIBlockElement::GetList($arReviewsSort, array("IBLOCK_ID" => REVIEWS_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_CAMPING_ID" => $arSection["ID"]), false, false, array("ID", "NAME", "ACTIVE_FROM", "DATE_CREATE", "DETAIL_TEXT", "PROPERTY_CAMPING_ID", "PROPERTY_PHOTOS", "PROPERTY_USER_ID", "PROPERTY_CRITERION_1", "PROPERTY_CRITERION_2", "PROPERTY_CRITERION_3", "PROPERTY_CRITERION_4", "PROPERTY_CRITERION_5", "PROPERTY_CRITERION_6", "PROPERTY_CRITERION_7", "PROPERTY_CRITERION_8", "PROPERTY_RATING"));
$arReviews = array();
$arReviewsUserIDs = array();
$reviewsCount = 0;
$reviewsCountNotNullRating = 0;
$avgRating = 0;
$isUserReview = 'N';
while ($arReview = $rsReviews->GetNext()) {
    foreach ($arReview["PROPERTY_PHOTOS_VALUE"] as $photoId) {
        $arReview["PICTURES"][] =  CFile::ResizeImageGet($photoId, array('width' => 1920, 'height' => 1080), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 80)["src"];
        $arReview["PICTURES_THUMB"][] = CFile::ResizeImageGet($photoId, array('width' => 125, 'height' => 87), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 80);
    }

    if ($arReview['PROPERTY_USER_ID_VALUE'] == $userId) {
        $isUserReview = 'Y';
    }

    $arButtons = CIBlock::GetPanelButtons($arReview["IBLOCK_ID"], $arReview["ID"], 0, array("SECTION_BUTTONS" => false, "SESSID" => false));
    $arReview["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
    $arReview["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

    $arReviews[$arReview["ID"]] = $arReview;

    for ($i = 1; $i <= 8; $i++) {
        if ($arReview["PROPERTY_CRITERION_" . $i . "_VALUE"] > 0) {
            $arAvgCriterias[$i][0]['value'] += $arReview["PROPERTY_CRITERION_" . $i . "_VALUE"];
            $arAvgCriterias[$i][0]['count'] += 1;
        }
    }

    $avgRating += $arReview["PROPERTY_RATING_VALUE"];
    $reviewsCount++;
    if ($arReview["PROPERTY_RATING_VALUE"] != 0) {
        $reviewsCountNotNullRating++;
    }

    if (!in_array($arReview["PROPERTY_USER_ID_VALUE"], $arReviewsUserIDs)) {
        $arReviewsUserIDs[] = $arReview["PROPERTY_USER_ID_VALUE"];
    }
}
if ($reviewsCount > 0) {
    // Средние значения
    for ($i = 1; $i <= 8; $i++) {
        if (!empty($arAvgCriterias[$i][0]['count'])) {
            $arAvgCriterias[$i][0] = number_format(round($arAvgCriterias[$i][0]['value'] / $arAvgCriterias[$i][0]['count'], 1), 1, '.', '');
            $arAvgCriterias[$i][1] = round($arAvgCriterias[$i][0] * 100 / 5);
        }
    }
    $avgRating = round($avgRating / ($reviewsCountNotNullRating ? $reviewsCountNotNullRating : 1), 1);

    // Список юзеров в отзывах
    $rsReviewsUsers = CUser::GetList(($by = "ID"), ($order = "ASC"), array("ID" => implode(' | ', $arReviewsUserIDs)), array("FIELDS" => array("ID", "NAME", "PERSONAL_PHOTO")));
    $arReviewsUsers = array();
    while ($arReviewUser = $rsReviewsUsers->Fetch()) {
        if ($arReviewUser["PERSONAL_PHOTO"]) {
            $arReviewUser["PERSONAL_PHOTO"] = CFile::GetFileArray($arReviewUser["PERSONAL_PHOTO"])["SRC"];
        }

        $arReviewsUsers[$arReviewUser["ID"]] = $arReviewUser;
    }

    // Пагинация отзывов
    $reviewsPage = $_REQUEST['reviewsPage'] ?? 1;
    $reviewsPageCount = ceil($reviewsCount / $arParams["DETAIL_REVIEWS_COUNT"]);
    if ($reviewsPageCount > 1) {
        $arReviews = array_slice($arReviews, ($reviewsPage - 1) * $arParams["DETAIL_REVIEWS_COUNT"], $arParams["DETAIL_REVIEWS_COUNT"], true);
    }

    // Лайки отзывов
    $arReviewsIDs = array_keys($arReviews);
    $arReviewsLikesData = Reviews::getLikes($arReviewsIDs);
    foreach ($arReviewsLikesData["STATS"] as $reviewId => $arLikes) {
        $arReviews[$reviewId]["LIKES"] = $arLikes;
    }
}

/* Номера */
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
if (!empty($arSection) && !empty($dateFrom) && !empty($dateTo) && !empty($_GET['guests'])) {
    $daysRange = $dateFrom . " - " . $dateTo;
    $daysCount = abs(strtotime($dateTo) - strtotime($dateFrom)) / 86400;

    // Фильтр номеров
    $arFilter = array(
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y",
        "SECTION_ID" => $arSection["ID"]
    );

    // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
    $arExternalResult = Products::searchRooms($arSection['ID'], $arSection['UF_EXTERNAL_ID'], $arSection['UF_EXTERNAL_SERVICE'], $guests, $arChildrenAge, $dateFrom, $dateTo, $arSection['UF_MIN_CHIELD_AGE']);
    $arExternalInfo = $arExternalResult['arRooms'];
    $searchError = $arExternalResult['error'];
    if ($arExternalInfo) {
        $arFilter["ID"] = array_keys($arExternalInfo);
    } else {
        $arFilter["ID"] = false;
    }

    // Список номеров
    $rsElements = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_TEXT", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_SQUARE", "PROPERTY_PARENT_ID", 'PROPERTY_ROOMS', 'PROPERTY_BEDS', 'PROPERTY_ROOMTOUR'));
    $arElements = array();
    while ($arElement = $rsElements->Fetch()) {
        if ($arElement["PROPERTY_PHOTOS_VALUE"]) {
            foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                $roomImageOriginal = CFile::GetFileArray($photoId);
                $arElement["PICTURES"][$photoId] = [
                    'src' => CFile::ResizeImageGet($photoId, array('width' => 464, 'height' => 328), BX_RESIZE_IMAGE_EXACT, true)['src'],
                    'big' => CFile::GetFileArray($photoId)["SRC"],
                ];
            }
        } else {
            $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
        }

        $roomElement = current($arExternalInfo[$arElement["ID"]]);
        $arElement["PRICE"] = $roomElement["price"];

        $discountData = CCatalogProduct::GetOptimalPrice($arElement['ID'], 1, $USER->GetUserGroupArray(), 'N');

        if (is_array($discountData['DISCOUNT']) && count($discountData['DISCOUNT'])) {
            $arElement['DISCOUNT_DATA']['VALUE'] = $discountData['DISCOUNT']['VALUE'];
            $arElement['DISCOUNT_DATA']['VALUE_TYPE'] = $discountData['DISCOUNT']['VALUE_TYPE'];
        }

        $arElements[$arElement['ID']] = $arElement;
    }

    if ($arSection["UF_EXTERNAL_SERVICE"] == "bnovo") {
        foreach ($arElements as $arElement) {
            if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0) {
                if (!isset($parentExternalIds) || !in_array(
                    $arElement["PROPERTY_PARENT_ID_VALUE"],
                    $parentExternalIds
                )) {
                    $parentExternalIds[] = $arElement["PROPERTY_PARENT_ID_VALUE"];
                }
            }
        }

        if (isset($parentExternalIds) && !empty($parentExternalIds)) {
            unset($arFilter["ID"]);
            $arFilter["?PROPERTY_EXTERNAL_ID"] = implode('|', $parentExternalIds);
            $rsElements = CIBlockElement::GetList(false, $arFilter, false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_TEXT", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_SQUARE", "PROPERTY_PARENT_ID"));
            while ($arElement = $rsElements->Fetch()) {
                if ($arElement["PROPERTY_PHOTOS_VALUE"]) {
                    foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                        $arElement["PICTURES"][$photoId] = [
                            'src' => CFile::ResizeImageGet($photoId, array('width' => 464, 'height' => 328), BX_RESIZE_IMAGE_EXACT, true)['src'],
                            'big' => CFile::GetFileArray($photoId)["SRC"],
                        ];
                    }
                } else {
                    $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
                }

                $arElementsParent[$arElement['PROPERTY_EXTERNAL_ID_VALUE']] = $arElement;
            }
        }
    }

    // Сортировка номеров по убыванию цены
    usort($arElements, function ($a, $b) {
        return ($a['PRICE'] - $b['PRICE']);
    });
    $arElementsJson = $arElements;

    if ($arSection["UF_EXTERNAL_SERVICE"] == "bnovo") {
        $arParams["DETAIL_ITEMS_COUNT"] = 999;
    }

    $minPrice = $arElements[0]['PRICE'];

    // Пагинация номеров
    $allCount = count($arElements);
    if ($allCount > 0) {
        $page = $_REQUEST['page'] ?? 1;
        $pageCount = ceil($allCount / $arParams["DETAIL_ITEMS_COUNT"]);
        if ($pageCount > 1) {
            $arElements = array_slice(
                $arElements,
                ($page - 1) * $arParams["DETAIL_ITEMS_COUNT"],
                $arParams["DETAIL_ITEMS_COUNT"]
            );
        }
    }
}

if (!isset($minPrice)) {
    $minPrice = $arSection['UF_MIN_PRICE'];
}

/* HL Blocks */
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
// Особенности объекта
$hlId = 5;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "filter" => ['ID' => $arSection['UF_FEATURES']],
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLFeatures = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLFeatures[$arEntity["UF_XML_ID"]] = $arEntity;
    $arHLFeaturesIds[] = $arEntity["UF_XML_ID"];
}
// Особенности номера
$hlId = 8;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
]);

$arHLRoomFeatures = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLRoomFeatures[$arEntity["UF_XML_ID"]] = $arEntity;
}

$houseTypesDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();
$houseTypeData = $houseTypesDataClass::query()
    ->addSelect('*')
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();
foreach ($houseTypeData as $houseType) {
    $arHouseTypes[$houseType['ID']] = $houseType;
}

// Удобства объекта
$objectComfortsDataClass = HighloadBlockTable::compileEntity(OBJECT_COMFORT_HL_ENTITY)->getDataClass();
$objectComfortsData = $objectComfortsDataClass::query()
    ->addSelect('*')
    ->where('ID', 'in', $arSection['UF_OBJECT_COMFORTS'])
    ->setOrder(['UF_SORT' => 'ASC'])
    ?->fetchAll();
foreach ($objectComfortsData as $objectComfort) {
    $arObjectComforts[$objectComfort['UF_XML_ID']] = $objectComfort;
    $arObjectComfortsIds[] = $objectComfort['UF_XML_ID'];
}

// Поиск детального описания удобства или развлечения
$featuresDetailList = \Bitrix\Iblock\Elements\ElementFeaturesdetailTable::getList([
    'select' => ['ID', 'NAME', 'FUN_VALUE' => 'FUN.VALUE', 'COMFORT_VALUE' => 'COMFORT.VALUE'],
    'filter' => [
        'OBJECT.VALUE' => $arSection['ID'],
        [
            "LOGIC" => "OR",
            ["COMFORT.VALUE" => $arObjectComfortsIds],
            ["FUN.VALUE" => $arHLFeaturesIds]
        ],
    ]
])->fetchAll();

foreach ($featuresDetailList as $featuresDetail) {
    if (isset($arObjectComforts[$featuresDetail['COMFORT_VALUE']])) {
        $arObjectComforts[$featuresDetail['COMFORT_VALUE']]['ELEMENT'] = $featuresDetail['ID'];
    }
    if (isset($arHLFeatures[$featuresDetail['FUN_VALUE']])) {
        $arHLFeatures[$featuresDetail['FUN_VALUE']]['ELEMENT'] = $featuresDetail['ID'];
    }
}

//

// Услуги (для Traveline)
$rsServices = CIBlockElement::GetList(array(), array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "PROPERTY_TRAVELINE" => getEnumIdByXml(SERVICES_IBLOCK_ID, 'TRAVELINE', 'Y')), false, false, array("ID", "NAME", "CODE"));
$arServicesTraveline = array();
while ($arService = $rsServices->Fetch()) {
    preg_match_all('/\d+/', $arService["CODE"], $matches);
    $code = $matches[0][0];
    $arServicesTraveline[$code] = $arService;
}

/* Генерация массива месяцев для фильтра */
$currMonthName = FormatDate("f");
$currYear = date('Y');
$nextYear = $currYear + 1;
$arDates = Products::getDates();

$currentURL = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];


/** faq */
$faqs = \Bitrix\Iblock\Elements\ElementObjectfaqTable::getList([
    'select' => ['NAME', 'PREVIEW_TEXT'],
    'order' => ['SORT' => 'ASC']
])->fetchAll();

/* Генерация SEO */
$fieldsSection = new \Bitrix\Iblock\InheritedProperty\SectionValues(CATALOG_IBLOCK_ID, $arSection['ID']);
$fieldsSectionValues  = $fieldsSection->getValues();

if (!empty($fieldsSectionValues)) {
    if (!empty($arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"])) {
        $typeObject = mb_strtolower($arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"], "UTF-8");
    } else {
        $typeObject = "";
    }

    $titleSEO = str_replace("#TYPE#", $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"], $fieldsSectionValues["SECTION_META_TITLE"]);
    $descriptionSEO = str_replace("#TYPE#", $typeObject, $fieldsSectionValues["SECTION_META_DESCRIPTION"]);
    $h1SEO = str_replace("#TYPE#", $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"], $fieldsSectionValues["SECTION_PAGE_TITLE"]);
} else {
    $titleSEO = $arSection["NAME"] . " - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист";
    $descriptionSEO = $arSection["NAME"] . " | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования.";
    $h1SEO = $arSection["~NAME"];
}

$APPLICATION->SetTitle($titleSEO);
$APPLICATION->AddHeadString('<meta name="description" content="' . $descriptionSEO . '" />');
/**/
?>

<main class="main object__detail">
    <section class="section section_crumbs">
        <div class="container">
            <?
            $APPLICATION->IncludeComponent(
                "naturalist:empty",
                "object_breadcrumbs",
                array(
                    "arSection" => $arSection,
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->


    <?
    $APPLICATION->IncludeComponent(
        "naturalist:empty",
        "object_hero",
        array(
            "arSection" => $arSection,
            "arFavourites" => $arFavourites,
            "currentURL" => $currentURL,
            "arHLTypes" => $arHLTypes,
            "dateFrom" => $dateFrom,
            "dateTo" => $dateTo,
            "arDates" => $arDates,
            "currMonthName" => $currMonthName,
            "currYear" => $currYear,
            "nextYear" => $nextYear,
            "guests" => $guests,
            "children" => $children,
            "guestsDeclension" => $guestsDeclension,
            "childrenDeclension" => $childrenDeclension,
            "arChildrenAge" => $arChildrenAge,
            "reviewsDeclension" => $reviewsDeclension,
            "reviewsCount" => $reviewsCount,
            "avgRating" => $avgRating,
            "arAvgCriterias" => $arAvgCriterias,
            "h1SEO" => $h1SEO,
            "arHLFeatures" => $arHLFeatures,
            "coords" => $coords,
            "arServices" => $arServices,
            "arAvgCriterias" => $arAvgCriterias,
            'houseTypeData' => $arHouseTypes,
            'allCount' => $allCount,
            "arHLRoomFeatures" => $arHLRoomFeatures,
            "arExternalInfo" => $arExternalInfo,
            "arElements" => $arElements,
            "arElementsJson" => $arElementsJson,
            "daysRange" => $daysRange,
            "page" => $page,
            "pageCount" => $pageCount,
            "daysDeclension" => $daysDeclension,
            "daysCount" => $daysCount,
            "arServicesTraveline" => $arServicesTraveline,
            "arElementsParent" => $arElementsParent ?? [],
            "arReviews" => $arReviews,
            "reviewsSortType" => $reviewsSortType,
            "arReviewsLikesData" => $arReviewsLikesData,
            "arReviewsUsers" => $arReviewsUsers,
            "reviewsPage" => $reviewsPage,
            "reviewsPageCount" => $reviewsPageCount,
            "isUserReview" => $isUserReview,
            'roomsDeclension' => $roomsDeclension,
            'bedsDeclension' => $bedsDeclension,
            'arObjectComforts' => $arObjectComforts,
        )
    );
    ?>

    <? if ($faqs) { ?>
        <section class="section section_faq">
            <div class="container">
                <div class="faq__title">FAQ</div>
                <ul class="faq__list list">
                    <? foreach ($faqs as $faq) { ?>
                        <li class="faq__item">
                            <div class="faq__item-title">
                                <?= $faq['NAME'] ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0697 7.99932C11.1326 6.93643 12.8675 6.93643 13.9304 7.99932L20.4504 14.5193C20.7433 14.8122 20.7433 15.2871 20.4504 15.58C20.1575 15.8729 19.6826 15.8729 19.3897 15.58L12.8697 9.05998C12.3926 8.58287 11.6075 8.58287 11.1304 9.05998L4.61041 15.58C4.31752 15.8729 3.84264 15.8729 3.54975 15.58C3.25685 15.2871 3.25685 14.8122 3.54975 14.5193L10.0697 7.99932Z" fill="black" />
                                </svg>
                            </div>
                            <div class="faq__item-content" style="display:none">
                                <?= $faq['PREVIEW_TEXT'] ?>
                            </div>
                        </li>
                    <? } ?>
                </ul>
            </div>
        </section>
    <? } ?>

    <section class="section section_related">
        <div class="container related-projects">
            <?
            global $arRelatedOffersFilter;
            $arRelatedOffersFilter = array(
                "!ID" => $arSection["ID"]
            );
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "catalog_offers",
                array(
                    "VIEW_MODE" => "TEXT",
                    "SHOW_PARENT_NAME" => "Y",
                    "IBLOCK_TYPE" => "",
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_URL" => "",
                    "FILTER_NAME" => "arRelatedOffersFilter",
                    "COUNT_ELEMENTS" => "Y",
                    "TOP_DEPTH" => "1",
                    "SECTION_FIELDS" => "",
                    "SECTION_USER_FIELDS" => array("UF_*"),
                    "ADD_SECTIONS_CHAIN" => "Y",
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "36000000",
                    "CACHE_NOTES" => "",
                    "CACHE_GROUPS" => "N",
                    "SECTION_RATING" => $avgRating,
                    "SECTION_COORDS" => explode(",", $arSection["UF_COORDS"]),
                    "RATING_RANGE" => 0.5,
                    "COORDS_RANGE" => 2,
                    "ITEMS_COUNT" => 8,
                    "AR_SECTION" => $arSection,
                    "arHLTypes" => $arHLTypes,
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->
    <? if (isset($arSection['UF_DOP_SEO_TEXT']) && $arSection['UF_DOP_SEO_TEXT'] != '') { ?>
        <section class="cert-index__seo-text">
            <div class="container">
                <?= $arSection['UF_DOP_SEO_TEXT'] ?>
            </div>
        </section>
        <div class="container">
            <a href="#" class="show-more-seo">Раскрыть</a>
        </div>
    <? } ?>
</main>

<?
$APPLICATION->IncludeComponent(
    "naturalist:empty",
    "object_modals",
    array(
        'SECTION_IMGS' => $arSection["PICTURES"],
        'TITLE' => $h1SEO,
        'OBJECT_COMFORTS' => $arObjectComforts,
        'OBJECT_FUN' => $arHLFeatures,
        'HOUSE_TYPES' => $arHouseTypes,
        'SECTION' => $arSection
    )
);
?>

<? if ($arSection["COORDS"]) : ?>
    <?
    $APPLICATION->IncludeComponent(
        "naturalist:empty",
        "object_scripts",
        array(
            "VARS" => array(
                "arSection" => $arSection,
                "arElements" => $arElements,
                "arExternalInfo" => $arExternalInfo,
                "arHLRoomFeatures" => $arHLRoomFeatures,
                "avgRating" => $avgRating,
                "minPrice" => $minPrice,
            ),
            "CACHE_TYPE" => "N",
        )
    );
    ?>
<? endif; ?>