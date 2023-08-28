<?
use Naturalist\Reviews;

global $arUser, $isAuthorized;
if (!$isAuthorized) {
    LocalRedirect('/');
}

$reviews = new Reviews();
$arReviews = $reviews->getListByUserId($arUser["ID"]);

$arResult = array(
    "arReviews" => $arReviews,
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized
);