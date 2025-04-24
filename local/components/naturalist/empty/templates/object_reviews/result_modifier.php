<?php

use Bitrix\Highloadblock\HighloadBlockTable;
use Naturalist\SmartWidgetsController;

/** @var  $arParams */

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
    "yandexReview" => $arParams['yandexReview'],
);