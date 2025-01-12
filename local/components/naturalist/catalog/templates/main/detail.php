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
$arDataFullGallery = [];

/** получение сезона из ИБ */
if (Cmodule::IncludeModule('asd.iblock')) {
    $arFields = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
} 



foreach($arFields['UF_SEASON'] as $season){
    if($season == 'Лето'){
        if ($arSection["UF_PHOTOS"]) {
            foreach ($arSection["UF_PHOTOS"] as $photoId) {
                $imageOriginal = CFile::GetFileArray($photoId);
                $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
        }
    }elseif($season == 'Зима'){
        if ($arSection["UF_WINTER_PHOTOS"]) {
            foreach ($arSection["UF_WINTER_PHOTOS"] as $photoId) {
                $imageOriginal = CFile::GetFileArray($photoId);
                $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            if ($arSection["UF_PHOTOS"]) {
                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                    $imageOriginal = CFile::GetFileArray($photoId);
                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
    }elseif($season == 'Осень+Весна'){
        if ($arSection["UF_MIDSEASON_PHOTOS"]) {
            foreach ($arSection["UF_MIDSEASON_PHOTOS"] as $photoId) {
                $imageOriginal = CFile::GetFileArray($photoId);
                $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            if ($arSection["UF_PHOTOS"]) {
                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                    $imageOriginal = CFile::GetFileArray($photoId);
                    $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
    }
}
unset($arSection["UF_PHOTOS"]);
$arSection["FULL_GALLERY"] = implode(",", $arDataFullGallery);

if ($arSection["UF_COORDS"]) {
    /* Метеоданные */
    $coords = $arSection["UF_COORDS"];
    $arMeteo = Users::getMeteo($coords);

    $arSection["COORDS"] = explode(',', $arSection["UF_COORDS"]);
}
/** Услуги */
if ($arSection["UF_SERVICES"]) {
    $rsServicesSections = CIBlockSection::GetList(false, array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "ACTIVE" => "Y"), false, array("ID", "NAME"), false);
    $arServicesSections = array();
    while ($arServicesSection = $rsServicesSections->Fetch()) {
        $arServicesSections[$arServicesSection["ID"]] = $arServicesSection["NAME"];
    }
    $rsServices = CIBlockElement::GetList(false, array("IBLOCK_ID" => SERVICES_IBLOCK_ID, "!IBLOCK_SECTION_ID" => false, "ID" => $arSection["UF_SERVICES"]), false, false, array("IBLOCK_ID", "IBLOCK_SECTION_ID", "ID", "NAME"));
    $arServices = array();
    while ($arService = $rsServices->Fetch()) {
        if (empty($arServices[$arService["IBLOCK_SECTION_ID"]])) {
            $arServices[$arService["IBLOCK_SECTION_ID"]] = array(
                "NAME" => $arServicesSections[$arService["IBLOCK_SECTION_ID"]],
                "ITEMS" => array()
            );
        }

        $arServices[$arService["IBLOCK_SECTION_ID"]]["ITEMS"][] = $arService;
    }
}

/** Питание */
if ($arSection["UF_FOOD"]) {
    $hlId = 12;
    Loader::IncludeModule('highloadblock');
    $hlblock = HighloadBlockTable::getById($hlId)->fetch();
    $campingFoodEntityClass = HighloadBlockTable::compileEntity($hlblock);
    $campingFoodEntityClass = $campingFoodEntityClass->getDataClass();

    $rsFood = $campingFoodEntityClass::getList([
        "select" => ["*"],
        "filter" => [
            "ID" => $arSection["UF_FOOD"]
        ],
        "order" => ["UF_SORT" => "ASC"],
    ]);
    while ($arFood = $rsFood->Fetch()) {
        if (empty($arServices["FOOD"])) {
            $arServices["FOOD"] = array(
                "NAME" => "Питание",
                "ITEMS" => array()
            );
        }
        $arFood["NAME"] = $arFood["UF_NAME"];
        $arServices["FOOD"]["ITEMS"][] = $arFood;
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
while ($arReview = $rsReviews->GetNext()) {
    foreach ($arReview["PROPERTY_PHOTOS_VALUE"] as $photoId) {
        $arReview["PICTURES"][] =  CFile::ResizeImageGet($photoId, array('width' => 1920, 'height' => 1080), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 80)["src"];
        $arReview["PICTURES_THUMB"][] = CFile::ResizeImageGet($photoId, array('width' => 70, 'height' => 50), BX_RESIZE_IMAGE_EXACT, true, false, false, 80);
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
    $rsElements = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter, false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "DETAIL_TEXT", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_SQUARE", "PROPERTY_PARENT_ID"));
    $arElements = array();
    while ($arElement = $rsElements->Fetch()) {
        if ($arElement["PROPERTY_PHOTOS_VALUE"]) {
            $arDataFullGalleryRoom = [];
            foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                $roomImageOriginal = CFile::GetFileArray($photoId);
                $arDataFullGalleryRoom[] = "&quot;" . $roomImageOriginal["SRC"] . "&quot;";
                $arElement["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
        }

        if (!empty($arDataFullGalleryRoom)) {
            $arElement["FULL_GALLERY_ROOM"] = implode(",", $arDataFullGalleryRoom);
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
                    $arDataFullGalleryRoom = [];
                    foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
                        $roomImageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGalleryRoom[] = "&quot;" . $roomImageOriginal["SRC"] . "&quot;";
                        $arElement["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true);
                    }
                } else {
                    $arElement["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/no_photo.png";
                }

                if (!empty($arDataFullGalleryRoom)) {
                    $arElement["FULL_GALLERY_ROOM"] = implode(",", $arDataFullGalleryRoom);
                }

                $arElementsParent[$arElement['PROPERTY_EXTERNAL_ID_VALUE']] = $arElement;
            }
        }
    }
    //die();
    // Сортировка номеров по убыванию цены
    usort($arElements, function ($a, $b) {
        return ($a['PRICE'] - $b['PRICE']);
    });
    $arElementsJson = $arElements;

    if ($arSection["UF_EXTERNAL_SERVICE"] == "bnovo" || $arSection["UF_EXTERNAL_SERVICE"] ==  'bronevik') {
        $arParams["DETAIL_ITEMS_COUNT"] = 999;
    }
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
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLFeatures = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLFeatures[$arEntity["ID"]] = $arEntity;
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
// Питание
$hlId = 12;
$hlblock = HighloadBlockTable::getById($hlId)->fetch();
$entity = HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();
$rsData = $entityClass::getList([
    "select" => ["*"],
    "order" => ["UF_SORT" => "ASC"],
]);
$arHLFood = array();
while ($arEntity = $rsData->Fetch()) {
    $arHLFood[$arEntity["UF_CODE"]] = $arEntity;
}
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

<main class="main">
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

    <section class="section section_object">
        <div class="container">
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
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->

    <? if ($arSection["UF_COORDS"] && $arMeteo) : ?>
        <section class="section section_info">
            <div class="container">
                <?
                $APPLICATION->IncludeComponent(
                    "naturalist:empty",
                    "object_info",
                    array(
                        "arMeteo" => $arMeteo,
                    )
                );
                ?>
            </div>
        </section>
        <!-- section-->
    <? endif; ?>

    <section class="section section_about" id="map">
        <div class="container">
            <?
            $APPLICATION->IncludeComponent(
                "naturalist:empty",
                "object_about",
                array(
                    "arSection" => $arSection,
                    "arHLFeatures" => $arHLFeatures,
                    "coords" => $coords,
                    "arServices" => $arServices,
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->

    <? if ($allCount > 0) : ?>
        <section class="section section_room" id="rooms-anchor">
            <div class="container">
                <?
                $APPLICATION->IncludeComponent(
                    "naturalist:empty",
                    "object_rooms",
                    array(
                        "VARS" => array(
                            "arSection" => $arSection,
                            "arElements" => $arElements,
                            "arElementsJson" => $arElementsJson,
                            "daysRange" => $daysRange,
                            "guests" => $guests,
                            "children" => $children,
                            "guestsDeclension" => $guestsDeclension,
                            "childrenDeclension" => $childrenDeclension,
                            "arServices" => $arServices,
                            "arHLRoomFeatures" => $arHLRoomFeatures,
                            "arHLFeatures" => $arHLFeatures,
                            "arHLFood" => $arHLFood,
                            "arExternalInfo" => $arExternalInfo,
                            "dateFrom" => $dateFrom,
                            "dateTo" => $dateTo,
                            "page" => $page,
                            "pageCount" => $pageCount,
                            "daysDeclension" => $daysDeclension,
                            "daysCount" => $daysCount,
                            "arServicesTraveline" => $arServicesTraveline,
                            "arElementsParent" => $arElementsParent ?? []
                        )
                    )
                );
                ?>
            </div>
        </section>
        <!-- section-->
    <? else : ?>
        <p class="search-error" style="display: none"><?= $searchError != '' ? $searchError : 'Не найдено номеров на выбранные даты' ?></p>
    <? endif; ?>

    <? if ($arReviews) : ?>
        <section class="section section_reviews" id="reviews-anchor">
            <div class="container">
                <?
                $APPLICATION->IncludeComponent(
                    "naturalist:empty",
                    "object_reviews",
                    array(
                        "avgRating" => $avgRating,
                        "reviewsDeclension" => $reviewsDeclension,
                        "reviewsCount" => $reviewsCount,
                        "arAvgCriterias" => $arAvgCriterias,
                        "reviewsSortType" => $reviewsSortType,
                        "arReviews" => $arReviews,
                        "arReviewsLikesData" => $arReviewsLikesData,
                        "arReviewsUsers" => $arReviewsUsers,
                        "reviewsPage" => $reviewsPage,
                        "reviewsPageCount" => $reviewsPageCount,
                    )
                );
                ?>
            </div>
        </section>
        <!-- section-->
    <? endif; ?>

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
    array()
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
            ),
            "CACHE_TYPE" => "N",
        )
    );
    ?>
<? endif; ?>