<?
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

$arParams["TYPE"] = "";
if(CModule::IncludeModule("highloadblock")){
    if(isset($arParams['arSection']["UF_TYPE_EXTRA"][0]) && !empty($arParams['arSection']["UF_TYPE_EXTRA"][0])){

        $hlbl = 2; // Указываем ID нашего highloadblock блока к которому будет делать запросы.
        $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

        $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
        $entity_data_class = $entity->getDataClass(); 

        $rsData = $entity_data_class::getList(array(
        "select" => array("*"),
        "order" => array("ID" => "ASC"),
        "filter" => array("ID"=> $arParams['arSection']["UF_TYPE_EXTRA"][0])
        ));

        while($arData = $rsData->Fetch()){
            $arParams["TYPE"] = $arData["UF_NAME"]." ";
        }
    }
} 

$arResult = array(
    "arSection" => $arParams['arSection'],
    "arFavourites" => $arParams['arFavourites'],
    "arHLTypes" => $arParams['arHLTypes'],
    "dateFrom" => $arParams['dateFrom'],
    "dateTo" => $arParams['dateTo'],
    "arDates" => $arParams['arDates'],
    "currMonthName" => $arParams['currMonthName'],
    "currYear" => $arParams['currYear'],
    "nextYear" => $arParams['nextYear'],
    "guests" => $arParams['guests'],
    "children" => $arParams['children'],
    "guestsDeclension" => $arParams['guestsDeclension'],
    "childrenDeclension" => $arParams['childrenDeclension'],
    "arChildrenAge" => $arParams['arChildrenAge'],
    "reviewsDeclension" => $arParams['reviewsDeclension'],
    "reviewsCount" => $arParams['reviewsCount'],
    "avgRating" => $arParams['avgRating'],
    "arAvgCriterias" => $arParams['arAvgCriterias'],
    "arHLFeatures" => $arParams['arHLFeatures'],
    "coords" => $arParams['coords'],
    "arServices" => $arParams['arServices'],
    "houseTypeData" => $arParams['houseTypeData'],
    'allCount' => $arParams['allCount'],
    "arHLRoomFeatures" => $arParams['arHLRoomFeatures'],
    "arExternalInfo" => $arParams['arExternalInfo'],
    "arElements" => $arParams['arElements'],
    "daysRange" => $arParams['daysRange'],
    "page" => $arParams['page'],
    "pageCount" => $arParams['pageCount'],
    "daysDeclension" => $arParams['daysDeclension'],
    "daysCount" => $arParams['daysCount'],
    "arElementsParent" => $arParams['arElementsParent'],
    "arReviews" => $arParams['arReviews'],
    "reviewsSortType" => $arParams['reviewsSortType'],
    "arReviewsLikesData" => $arParams['arReviewsLikesData'],
    "arReviewsUsers" => $arParams['arReviewsUsers'],
    "reviewsPage" => $arParams['reviewsPage'],
    "reviewsPageCount" => $arParams['reviewsPageCount'],
    "isUserReview" => $arParams['isUserReview'],
    'roomsDeclension' => $arParams['roomsDeclension'],
    'bedsDeclension' => $arParams['bedsDeclension'],
    'arObjectComforts' => $arParams['arObjectComforts'],
    'searchError' => $arParams['searchError'],
);

