<?
use Naturalist\Users;
use Naturalist\Reviews;
use Naturalist\Products;
use Bitrix\Main\Application;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Web\Uri;
use Bitrix\Highloadblock\HighloadBlockTable;

global $arUser, $isAuthorized;
global $arFavourites;

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

/* Получение избранного */
$arFavouritesData = array();
if ($arFavourites) {
    $guestsDeclension = new Declension('гость', 'гостя', 'гостей');

    $arUriParams = array(
        'dateFrom' => $_GET['dateFrom'],
        'dateTo' => $_GET['dateTo'],
        'guests' => $_GET['guests'],
        'children' => $_GET['children'],
        'childrenAge' => $_GET['childrenAge'],
    );

    /* Отзывы */
    $arReviewsAvg = Reviews::getCampingRating($arFavourites);

    /* Получение избранного */
    $arFilter = array(
        "IBLOCK_ID" => CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y",
        "ID" => $arFavourites
    );
    // Заезд, выезд, кол-во гостей
    $dateFrom = $_GET['dateFrom'];
    $dateTo = $_GET['dateTo'];
    $guests = $_GET['guests'] ?? 2;
    $children = $_GET['children'] ?? 0;
    $arChildrenAge = (isset($_GET['childrenAge'])) ? explode(',' , $_GET['childrenAge']) : [];
    if (!empty($dateFrom) && !empty($dateTo) && !empty($_GET['guests'])) {
        $daysCount = abs(strtotime($dateTo) - strtotime($dateFrom)) / 86400;

        // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
        $arExternalInfo = Products::search($guests, $arChildrenAge, $dateFrom, $dateTo, false);
        $arExternalIDs = array_keys($arExternalInfo);
        if($arExternalIDs) {
            $arFilter["UF_EXTERNAL_ID"] = $arExternalIDs;
        } else {
            $arFilter["UF_EXTERNAL_ID"] = false;
        }
    }

    /** получение сезона из ИБ */
    if (Cmodule::IncludeModule('asd.iblock')) {
        $arFields = CASDiblockTools::GetIBUF(CATALOG_IBLOCK_ID);
    }

    $rsFavourites = CIBlockSection::GetList(array(), $arFilter, false, array("IBLOCK_ID", "ID", "NAME", "CODE", "SECTION_PAGE_URL", "UF_*"), false);
    while ($arFavourite = $rsFavourites->GetNext()) {
        /*if($arFavourite["UF_PHOTOS"]) {
            foreach ($arFavourite["UF_PHOTOS"] as $photoId) {
                $arFavourite["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true);
            }

        } else {
            $arFavourite["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH."/img/no_photo.png";
        }*/
        foreach($arFields['UF_SEASON'] as $season){
            if($season == 'Лето'){
                if ($arFavourite["UF_PHOTOS"]) {
                    foreach ($arFavourite["UF_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $arFavourite["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                    }
                } else {
                    $arFavourite["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                }
            }elseif($season == 'Зима'){
                if ($arFavourite["UF_WINTER_PHOTOS"]) {
                    foreach ($arFavourite["UF_WINTER_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $arFavourite["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                    }
                } else {
                    if ($arFavourite["UF_PHOTOS"]) {
                        foreach ($arFavourite["UF_PHOTOS"] as $photoId) {
                            $imageOriginal = CFile::GetFileArray($photoId);
                            $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                            $arFavourite["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                        }
                    } else {
                        $arSearFavouritection["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                    }
                }
            }elseif($season == 'Осень+Весна'){
                if ($arFavourite["UF_MIDSEASON_PHOTOS"]) {
                    foreach ($arFavourite["UF_MIDSEASON_PHOTOS"] as $photoId) {
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                        $arFavourite["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                    }
                } else {
                    if ($arFavourite["UF_PHOTOS"]) {
                        foreach ($arFavourite["UF_PHOTOS"] as $photoId) {
                            $imageOriginal = CFile::GetFileArray($photoId);
                            $arDataFullGallery[] = "\"" . $imageOriginal["SRC"] . "\"";
                            $arFavourite["PICTURES"][$photoId] = CFile::ResizeImageGet($photoId, array('width' => 590, 'height' => 390), BX_RESIZE_IMAGE_EXACT, true);
                        }
                    } else {
                        $arFavourite["PICTURES"][0]["src"] = SITE_TEMPLATE_PATH . "/img/big_no_photo.png";
                    }
                }
            }
        }
        unset($arFavourite["UF_PHOTOS"]);
        $arFavourite["FULL_GALLERY"] = implode(",", $arDataFullGallery);

        $arFavourite["RATING"] = (isset($arReviewsAvg[$arFavourite["ID"]])) ? $arReviewsAvg[$arFavourite["ID"]]["avg"] : 0;
        $arFavourite["REVIEWS_COUNT"] = (isset($arReviews[$arFavourite["ID"]])) ? $arReviewsAvg[$arFavourite["ID"]]["count"] : 0;

        if ($arFavourite["UF_COORDS"]) {
            $arFavourite["COORDS"] = explode(',', $arFavourite["UF_COORDS"]);
        }

        if($arExternalInfo) {
            $sectionPrice = $arExternalInfo[$arFavourite["UF_EXTERNAL_ID"]];
            // Если это Traveline, то делим цену на кол-во дней
            if($arFavourite["UF_EXTERNAL_SERVICE"] == 1) {
                $sectionPrice = round($sectionPrice / $daysCount);
            }

        } else {
            $sectionPrice = $arFavourite["UF_MIN_PRICE"];
        }
        $arFavourite["PRICE"] = $sectionPrice;

        $uri = new Uri($arFavourite["SECTION_PAGE_URL"]);
        $uri->addParams($arUriParams);
        $sectionUrl = $uri->getUri();
        $arFavourite["URL"] = $sectionUrl;

        $arFavouritesData[$arFavourite["ID"]] = $arFavourite;
    }
}

/* Генерация массива месяцев для фильтра */
$currMonthName = FormatDate("f");
$currYear = date('Y');
$nextYear = $currYear + 1;
$arDates = Products::getDates();

$arResult = array(
    "dateFrom" => $dateFrom,
    "dateTo" => $dateTo,
    "guests" => $guests,
    "children" => $children,
    "arChildrenAge" => $arChildrenAge,
    "daysCount" => $daysCount,
    "guestsDeclension" => $guestsDeclension,
    "arFavourites" => $arFavouritesData,
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized,
    "currMonthName" => $currMonthName,
    "currYear" => $currYear,
    "nextYear" => $nextYear,
    "arDates" => $arDates,
    "arUriParams" => $arUriParams,
    "arHLTypes" => $arHLTypes,
);