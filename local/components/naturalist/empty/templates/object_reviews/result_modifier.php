<?php

use Bitrix\Highloadblock\HighloadBlockTable;

$commonYandexReviewsClass = HighloadBlockTable::compileEntity('YandexReviews')->getDataClass();
$commonYandexReviews = $commonYandexReviewsClass::query()
    ->addSelect('*')
    ->setOrder(['ID' => 'ASC'])
    ->setFilter(['UF_ID_OBJECT' => $arResult["sectionId"]])
    ->setCacheTtl(36000000)
    ?->fetchAll();

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
    "reviewsYandex" => $commonYandexReviews,
    "sectionId" => $arParams['sectionId'],
    "isUserReview" => $arParams['isUserReview'],
);