<?
use Naturalist\Reviews;

global $arUser, $isAuthorized;
if (!$isAuthorized) {
    LocalRedirect('/');
}

$reviews = new Reviews();
$arReviews = $reviews->getListByUserId($arUser["ID"]);

$arCertReviews = $reviews->getList(array("ID" => "DESC"), array("PROPERTY_USER_ID" => $arUser["ID"]), [], CERT_REVIEWS_IBLOCK_ID);

$arReviews = array_merge($arReviews, $arCertReviews);

$arResult = array(
    "arReviews" => $arReviews,
    "arUser" => $arUser,
    "isAuthorized" => $isAuthorized
);