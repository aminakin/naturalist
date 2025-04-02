<?php
use Bitrix\Highloadblock\HighloadBlockTable;

$arResult = array(
    "avgRating" => $arParams['avgRating'],
    "reviewsDeclension" => $arParams['reviewsDeclension'],
    "reviewsCount" => $arParams['reviewsCount'],
    "arAvgCriterias" => $arParams['arAvgCriterias'],
    "reviewsSortType" => $arParams['reviewsSortType'],
    "arReviews" => $arParams['arReviews'],
    "arReviewsLikesData" => $arParams['arReviewsLikesData'],
    "arReviewsUsers" => $arParams['arReviewsUsers'],
    "reviewsPage" => $arParams['reviewsPage'],
    "reviewsPageCount" => $arParams['reviewsPageCount'],
    "sectionId" => $arParams['sectionId'],
    "isUserReview" => $arParams['isUserReview'],
);


$commonYandexReviewsClass = HighloadBlockTable::compileEntity('YandexReviews')->getDataClass();
$commonYandexReviews = $commonYandexReviewsClass::query()
    ->addSelect('*')
    ->setOrder(['ID' => 'ASC'])
    ->setFilter(['ID' => $arResult["sectionId"]])
    ->setCacheTtl(36000000)
    ?->fetchAll();

    
echo '<pre>';
var_export($commonYandexReviews);
echo '</pre>';