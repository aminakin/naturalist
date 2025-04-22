<?

use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Registry;
use Bitrix\Main\Context;
use Naturalist\Users;
use Naturalist\Baskets;
use Naturalist\Orders;

global $arUser, $userId, $isAuthorized, $APPLICATION;

$baskets = new Baskets();
$arBasket = $baskets->get();
/*if(count($arBasket["ITEMS"]) > 1) {
    for ($i = 0; $i < count($arBasket["ITEMS"])-1; $i++) {
        $baskets->delete($arBasket["ITEMS"][$i]["ID"]);
    }

    $arBasket = $baskets->get();
}
if ($arBasket["ITEMS"][0]["QUANTITY"] > $arBasket["ITEMS"][0]["PROPS"]["DAYS_COUNT"]) {
    $baskets->update($arBasket["ITEMS"][0]["ID"], $arBasket["ITEMS"][0]["PROPS"]["DAYS_COUNT"]);
    $arBasket = $baskets->get();
}*/
//xprint($arBasket); die();

$elementId = $arBasket["ITEMS"][0]["PRODUCT_ID"];
$sectionId = $arBasket["ITEMS"][0]["ITEM"]["IBLOCK_SECTION_ID"];
$dateFrom = $arBasket["ITEMS"][0]["PROPS"]["DATE_FROM"];
$dateTo = $arBasket["ITEMS"][0]["PROPS"]["DATE_TO"];
$guests = $arBasket["ITEMS"][0]["PROPS"]["GUESTS_COUNT"];
$childrenAge = $arBasket["ITEMS"][0]["PROPS"]["CHILDREN"];
$children = !empty($childrenAge) ? count(explode(',', $childrenAge)) : 0;
$daysCount = $arBasket["ITEMS"][0]["PROPS"]["DAYS_COUNT"];
$categoryId = $arBasket["ITEMS"][0]["PROPS"]["CATEGORY_ID"];
$checksum = $arBasket["ITEMS"][0]["PROPS"]["CHECKSUM"];
$totalPrice = $arBasket["DATA"]["TOTAL_PRICE"];
$tariffValue = $arBasket["ITEMS"][0]["PROPS"]["TARIFF_ID"];
$prices = $arBasket["ITEMS"][0]["PROPS"]["PRICES"];

/* Склонения */
$guestsDeclension = new Declension('гость', 'гостя', 'гостей');
$childrenDeclension = new Declension('ребенок', 'детей', 'детей');
$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');
$daysDeclension = new Declension('ночь', 'ночи', 'ночей');

/* Текущий раздел */
$arSection = CIBlockSection::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $sectionId), false, array("IBLOCK_ID", "ID", "NAME", "CODE", "DESCRIPTION", "SECTION_PAGE_URL", "UF_*"), false)->GetNext();

/** получение сезона из ИБ */
if (Cmodule::IncludeModule('asd.iblock')) {
    $arFields = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
}

foreach ($arFields['UF_SEASON'] as $season) {
    if ($season == 'Лето') {
        if ($arSection["UF_PHOTOS"]) {
            foreach ($arSection["UF_PHOTOS"] as $photoId) {
                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            if ($arSection["UF_WINTER_PHOTOS"]) {
                foreach ($arSection["UF_WINTER_PHOTOS"] as $photoId) {
                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
    } elseif ($season == 'Зима') {
        if ($arSection["UF_WINTER_PHOTOS"]) {
            foreach ($arSection["UF_WINTER_PHOTOS"] as $photoId) {
                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            if ($arSection["UF_PHOTOS"]) {
                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
    } elseif ($season == 'Осень+Весна') {
        if ($arSection["UF_MIDSEASON_PHOTOS"]) {
            foreach ($arSection["UF_MIDSEASON_PHOTOS"] as $photoId) {
                $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
            }
        } else {
            if ($arSection["UF_PHOTOS"]) {
                foreach ($arSection["UF_PHOTOS"] as $photoId) {
                    $arSection["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                }
            } else {
                $arSection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
            }
        }
    }
}
unset($arSection["UF_PHOTOS"]);
/* Текущий элемент */
$arElement = CIBlockElement::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $elementId), false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "CATALOG_PRICE_1", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_GUESTS_COUNT", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_PARENT_ID"))->Fetch();
foreach ($arElement["PROPERTY_PHOTOS_VALUE"] as $photoId) {
    $arElement["PICTURES"][$photoId] = CFile::GetFileArray($photoId);
}

if ($arSection["UF_EXTERNAL_SERVICE"] == 2) {
    if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0) {
        $arElementParent = CIBlockElement::GetList(false, array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_EXTERNAL_ID" => $arElement["PROPERTY_PARENT_ID_VALUE"]), false, false, array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "CATALOG_PRICE_1", "PROPERTY_PHOTOS", "PROPERTY_FEATURES", "PROPERTY_GUESTS_COUNT", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID", "PROPERTY_PARENT_ID"))->Fetch();
        $arElement["NAME"] = $arElementParent["NAME"];
        foreach ($arElementParent["PROPERTY_PHOTOS_VALUE"] as $photoId) {
            $arElement["PICTURES"][$photoId] = CFile::GetFileArray($photoId);
        }
    }
}

if (!$arSection || !$arElement || !$dateFrom || !$dateTo || !$guests) {
    LocalRedirect('/');
}

/* Отзывы */
$rsReviews = CIBlockElement::GetList($arReviewsSort, array("IBLOCK_ID" => REVIEWS_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_CAMPING_ID" => $arSection["ID"]), false, false, array("ID", "PROPERTY_RATING"));
$arReviews = array();
$reviewsCount = 0;
while ($arReview = $rsReviews->GetNext()) {
    $arReviews[$arReview["ID"]] = $arReview;

    $avgRating += $arReview["PROPERTY_RATING_VALUE"];
    $reviewsCount++;
}
if ($reviewsCount > 0) {
    $avgRating = round($avgRating / $reviewsCount, 1);
}

/* HL Blocks */
// Types
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
// Camping features
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

$arGuestsNamesData = [1 => 'Основной', 'Второй', 'Третий', 'Четвертый', 'Пятый'];

/* Получаем активные купоны */
$orders = new Orders();
$coupons = $orders->getActivatedCoupons();
if (intval(Users::getInnerScore()) !== 0 && is_array($coupons) && count($coupons)) {
    foreach ($coupons as $coupon) {
        $orders->removeCoupon($coupon['COUPON']);
    }
}

/* Считаем скидки */
$basket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());
$registry = Registry::getInstance(Registry::REGISTRY_TYPE_ORDER);
$orderClass = $registry->getOrderClassName();
$order = $orderClass::create(Context::getCurrent()->getSite(), $USER->getId());
$result = $order->appendBasket($basket);
$discounts = $order->getDiscount();
$showPrices = $discounts->getShowPrices();
foreach ($showPrices['BASKET'] as $finalprices) {
    $finalBaskePrices = $finalprices;
}

/* Проверяем куки на наличие данных из формы */
global $APPLICATION;
$coockieName = $APPLICATION->get_cookie("orderName");
$coockieSurname = $APPLICATION->get_cookie("orderSurname");
$coockiePhone = $APPLICATION->get_cookie("orderPhone");
$coockieEmail = $APPLICATION->get_cookie("orderEmail");
$coockieComment = $APPLICATION->get_cookie("orderComment");

if ($coockieName != '') {
    $arUser['NAME'] = $coockieName;
}

if ($coockieSurname != '') {
    $arUser['LAST_NAME'] = $coockieSurname;
}

if ($coockiePhone != '') {
    $arUser['PERSONAL_PHONE'] = $coockiePhone;
}

if ($coockieEmail != '') {
    $arUser['EMAIL'] = $coockieEmail;
}

if ($coockieComment != '') {
    $arUser['COMMENT'] = $coockieComment;
}


$paySystemResult = \Bitrix\Sale\PaySystem\Manager::getList(array(
    'filter'  => array(
        'ACTIVE' => 'Y',
        '!ACTION_FILE' => 'inner',
        '!CODE' => ['CERT', 'write_off', 'aladdin', 'yandex', 'flowwow', 'giftery', 'digift'],
    ),
    'order' => ['sort' => 'asc']
))->fetchAll();

$arResult = array(
    "elementId" => $elementId,
    "sectionId" => $sectionId,
    "dateFrom" => $dateFrom,
    "dateTo" => $dateTo,
    "guests" => $guests,
    "children" => $children,
    "childrenAge" => $childrenAge,
    "daysCount" => $daysCount,
    "categoryId" => $categoryId,
    "checksum" => $checksum,
    "totalPrice" => $totalPrice,
    "tariffValue" => $tariffValue,
    "childrenDeclension" => $childrenDeclension,
    "guestsDeclension" => $guestsDeclension,
    "reviewsDeclension" => $reviewsDeclension,
    "daysDeclension" => $daysDeclension,
    "arSection" => $arSection,
    "arElement" => $arElement,
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized,
    "avgRating" => $avgRating,
    "reviewsCount" => $reviewsCount,
    "arHLTypes" => $arHLTypes,
    "arHLFeatures" => $arHLFeatures,
    "arGuestsNamesData" => $arGuestsNamesData,
    "coupons" => $coupons,
    "finalPrice" => $finalBaskePrices,
    "paySystems" => $paySystemResult,
);
if (!empty($prices)) {
    $arResult["priceOneNight"] = array_shift(unserialize($prices));
}
