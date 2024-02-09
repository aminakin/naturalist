<?
use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $arUser, $isAuthorized;
global $arSettings;
global $arFavourites;

global $currPage;
$currPage = $APPLICATION->GetCurPage();

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/jquery-3.6.1.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/common.js");

use Naturalist\Users;

?><!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">

<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5762ML9');</script>
    <!-- End Google Tag Manager -->
    
    <meta charset="<?= LANG_CHARSET ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/Montserrat-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/Montserrat-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-Bd.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-DmBd.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-Md.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-Regular.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/app.css?v=1667839051330">
    <? if (CSite::InDir('/index.php')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/index.css?v=1666595102636">
    <? endif; ?>
    <? if (CSite::InDir('/catalog')) : ?>
        <?if($currPage === "/catalog/" || strpos($currPage,"/catalog/vpechatleniya/") !== false):?>
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=1664304519938">
        <?else:?>
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/object.css?v=1667839051348">
        <?endif;?>
    <? endif; ?>
    <? if (CSite::InDir('/order')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/reservation.css?v=1664554796110">
    <? endif; ?>
    <? if (CSite::InDir('/personal')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_person.css?v=1664554795104">
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_person_values.css?v=1664554795104">
    <? endif; ?>
    <? if (CSite::InDir('/personal/reviews')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_reviews.css?v=1664554795394">
    <? endif; ?>
    <? if (CSite::InDir('/personal/active')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_active.css?v=1664554794106">
    <? endif; ?>
    <? if (CSite::InDir('/personal/history')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_history.css?v=1664554794775">
    <? endif; ?>
    <? if (CSite::InDir('/personal/favourites')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_favorite.css?v=1664554794433">
    <? endif; ?>
    <? if (CSite::InDir('/about')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/about.css?v=1664554795104">
    <? endif; ?>
    <? if (CSite::InDir('/impressions')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/impressions.css?v=1664554795104">
    <? endif; ?>
    <? if (CSite::InDir('/contacts')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/contacts.css?v=1664554795104">
    <? endif; ?>
    <? if (CSite::InDir('/map')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=1664304519938">
    <? endif; ?>
    <? if (CSite::InDir('/objects')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/add_object.css?v=1664304519938">
    <? endif; ?>

    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/custom.css?v=1664304519938">

    <? $APPLICATION->ShowHead() ?>
    <script type="text/javascript">!function () {
            var t = document.createElement("script");
            t.type = "text/javascript", t.async = !0, t.src = 'https://vk.com/js/api/openapi.js?169', t.onload = function () {
                VK.Retargeting.Init("VK-RTRG-1591008-9HAMu"), VK.Retargeting.Hit()
            }, document.head.appendChild(t)
        }();</script>
    <noscript><img src="https://vk.com/rtrg?p=VK-RTRG-1591008-9HAMu" style="position:fixed; left:-999px;" alt=""/>
    </noscript>

    
    <!-- Top.Mail.Ru counter -->
        <script type="text/javascript">
            var _tmr = window._tmr || (window._tmr = []);
            _tmr.push({id: "3480625", type: "pageView", start: (new Date()).getTime()});
            (function (d, w, id) {
                if (d.getElementById(id)) return;
                var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
                ts.src = "https://top-fwz1.mail.ru/js/code.js";
                var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
                if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
            })(document, window, "tmr-code");
        </script>
        <noscript><div><img src="https://top-fwz1.mail.ru/counter?id=3480625;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div></noscript>
    <!-- /Top.Mail.Ru counter -->

</head>

<body class=" <?php if (CSite::InDir('/map')): ?>body__on_map<?php endif; ?>">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5762ML9"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


    <div id="admin_panel"><? $APPLICATION->ShowPanel(); ?></div>


<div class="wrapper">
    <header class="header" data-scroll-fixed>
        <div class="container">
            <div class="header__logo">
                <a class="logotype" href="/"><img src="<?=$arSettings['header_logo']?>" alt="<?=$arSettings['header_logo_name']?>"></a>
            </div>

            <button class="header__toggler" data-menu-open type="button"><span></span></button>
            <div class="header__menu">
                <nav class="navigation" data-menu>
                    <button class="navigation__close" data-menu-close type="button">
                        <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                            <use xlink:href="#cross" />
                        </svg>
                    </button>
                    <ul class="list">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "header",
                            array(
                                "ROOT_MENU_TYPE" => "header",
                                "MAX_LEVEL" => "2",
                                "CHILD_MENU_TYPE" => "sub",
                                "USE_EXT" => "Y",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "Y",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "MENU_CACHE_GET_VARS" => ""
                            ),
                            false
                        );
                        ?>
                    </ul>
                </nav>
            </div>

            <div class="header__controls">
                <ul class="list">
                    <li class="list__item list__item_favorite">
                        <a class="list__link" href="<?=$arSettings['header_favourites_link']?>">
                            <div class="list__item-icon">
                                <svg class="icon icon_heart" viewbox="0 0 10 8" style="width: 1rem; height: 0.8rem;">
                                    <use xlink:href="#heart"/>
                                </svg>
                                <span><?=($arFavourites) ? count($arFavourites) : 0?></span>
                            </div>
                            <span><?=$arSettings['header_favourites_name']?></span>
                        </a>
                    </li>

                    <? if (!$isAuthorized): ?>
                        <li class="list__item">
                            <a class="list__link" href="#login-phone" data-modal>
                                <svg class="icon icon_person" viewbox="0 0 16 16"
                                     style="width: 1.6rem; height: 1.6rem;">
                                    <use xlink:href="#person"/>
                                </svg>
                                <span><?= $arSettings['header_personal_name'] ?></span>
                            </a>
                        </li>
                    <? else: ?>
                        <li class="list__item">
                            <a class="list__link" href="<?= $arSettings['header_personal_link'] ?>">
                                <? if ($arUser["PERSONAL_PHOTO"]):
                                    $arUser["PERSONAL_PHOTO"] = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"]["ID"], array('width' => 96, 'height' => 96), BX_RESIZE_IMAGE_EXACT, true);
                                    ?>
                                    <div class="list__item-icon">
                                        <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>"
                                             alt="<?= $arUser["NAME"] ?>">
                                    </div>
                                <? else: ?>
                                    <svg class="icon icon_person" viewbox="0 0 16 16"
                                         style="width: 1.6rem; height: 1.6rem;">
                                        <use xlink:href="#person"/>
                                    </svg>
                                <? endif; ?>
                                <span><?= $arUser["NAME"] ?? $arSettings['header_personal_name'] ?></span>
                            </a>
                        </li>
                    <? endif; ?>

                    <? if ($isAuthorized): ?>
                        <li class="list__item highlight_orange cert_balance">
                            <span class="list__link">
                                <?= $arSettings['header_balance_certification'] ?>
                                <?= number_format(Users::getInnerScore(), 0, '.', ' ')?>
                                ₽
                            </span>
                        </li>
                    <? endif; ?>

                    <li class="list__item list__item_desktop">
                        <a class="list__link" href="#feedback" data-modal>
                            <svg class="icon icon_message" viewbox="0 0 24 24" style="width: 2.4rem; height: 2.4rem;">
                                <use xlink:href="#message" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- header-->
