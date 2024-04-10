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

?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">

<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5762ML9');
    </script>
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

    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/app.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/app.css');?>">
    <? if (CSite::InDir('/index.php')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/index.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/index.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/catalog')) : ?>
        <? if ($currPage === "/catalog/" || strpos($currPage, "/catalog/vpechatleniya/") !== false) : ?>
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/catalog.css');?>">
        <? else : ?>
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/object.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/object.css');?>">
        <? endif; ?>
    <? endif; ?>
    <? if (CSite::InDir('/order')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/reservation.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/reservation.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/personal')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_person.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/lk_person.css');?>">
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_person_values.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/lk_person_values.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/personal/reviews')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_reviews.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/lk_reviews.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/personal/active')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_active.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/lk_active.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/personal/history')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_history.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/lk_history.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/personal/favourites')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_favorite.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/lk_favorite.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/about')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/about.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/about.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/impressions')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/impressions.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/impressions.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/map')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/catalog.css');?>">
    <? endif; ?>
    <? if (CSite::InDir('/objects')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/add_object.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/add_object.css');?>">
    <? endif; ?>

    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/custom.css?v=<?=filemtime($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/assets/css/custom.css');?>">

    <? if (CSite::InDir('/promo')) : ?>
        <!-- Marquiz script start --> 
        <script>
            (function(w, d, s, o) {
                var j = d.createElement(s);
                j.async = true;
                j.src = '//script.marquiz.ru/v2.js';
                j.onload = function() {
                    if (document.readyState !== 'loading') Marquiz.init(o);
                    else document.addEventListener("DOMContentLoaded", function() {
                        Marquiz.init(o);
                    });
                };
                d.head.insertBefore(j, d.head.firstElementChild);
            })(window, document, 'script', {
                host: '//quiz.marquiz.ru',
                region: 'eu',
                id: '65eede6633bbfa00269d9ae5',
                autoOpen: 10,
                autoOpenFreq: 'once',
                openOnExit: false,
                disableOnMobile: false
            });
        </script>
        <!-- Marquiz script end -->
    <? endif; ?>

    <? $APPLICATION->ShowHead() ?>
    <script type="text/javascript">
        ! function() {
            var t = document.createElement("script");
            t.type = "text/javascript", t.async = !0, t.src = 'https://vk.com/js/api/openapi.js?169', t.onload = function() {
                VK.Retargeting.Init("VK-RTRG-1591008-9HAMu"), VK.Retargeting.Hit()
            }, document.head.appendChild(t)
        }();
    </script>
    <noscript><img src="https://vk.com/rtrg?p=VK-RTRG-1591008-9HAMu" style="position:fixed; left:-999px;" alt="" />
    </noscript>


    <!-- Top.Mail.Ru counter -->
    <script type="text/javascript">
        var _tmr = window._tmr || (window._tmr = []);
        _tmr.push({
            id: "3480625",
            type: "pageView",
            start: (new Date()).getTime()
        });
        (function(d, w, id) {
            if (d.getElementById(id)) return;
            var ts = d.createElement("script");
            ts.type = "text/javascript";
            ts.async = true;
            ts.id = id;
            ts.src = "https://top-fwz1.mail.ru/js/code.js";
            var f = function() {
                var s = d.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(ts, s);
            };
            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else {
                f();
            }
        })(document, window, "tmr-code");
    </script>
    <noscript>
        <div><img src="https://top-fwz1.mail.ru/counter?id=3480625;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div>
    </noscript>
    <!-- /Top.Mail.Ru counter -->

    <?
if (!\Bitrix\Main\Engine\CurrentUser::get()->isAdmin()) {
?>

    <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(91071014, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/91071014" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->



<? } ?>

    <script src="https://dmp.one/sync?stock_key=4dce2e8f5fdd1727a46278cb20b97261" async charset="UTF-8"></script>
</head>

<body class=" <?php if (CSite::InDir('/map')) : ?>body__on_map<?php endif; ?>">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5762ML9" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->


    <div id="admin_panel"><? $APPLICATION->ShowPanel(); ?></div>


    <div class="wrapper">
        <header class="header" data-scroll-fixed>
            <div class="container">
                <div class="header__logo">
                    <a class="logotype" href="/"><img src="<?= $arSettings['header_logo'] ?>" alt="<?= $arSettings['header_logo_name'] ?>"></a>
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
                        <li class="list__item list__item_phone">                            
                            <a href="tel:<?= $arSettings['contacts_phone'] ?>">
                                <span><?= $arSettings['contacts_phone'] ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
                                    <path d="M23.898 4.09901C18.4298 -1.36749 9.56551 -1.36618 4.09901 4.10201C-1.36749 9.57021 -1.36618 18.4345 4.10202 23.901C9.57021 29.3675 18.4345 29.3662 23.901 23.898C26.5263 21.2718 28.0008 17.7103 28 13.997C27.9992 10.2843 26.5237 6.72389 23.898 4.09901ZM21.2025 19.5018C21.2019 19.5024 21.2013 19.5031 21.2006 19.5037V19.499L20.4913 20.2037C19.5739 21.1327 18.2378 21.5149 16.9679 21.2117C15.6885 20.8692 14.4723 20.3241 13.3653 19.597C12.3368 18.9398 11.3838 18.1714 10.5233 17.3057C9.73159 16.5198 9.02033 15.6568 8.39995 14.7297C7.72139 13.7321 7.18431 12.6453 6.80396 11.5003C6.36793 10.1553 6.72925 8.6793 7.73731 7.68771L8.56796 6.85706C8.7989 6.62507 9.17416 6.62425 9.40609 6.8552C9.4067 6.8558 9.40735 6.8564 9.40795 6.85706L12.0306 9.4797C12.2626 9.71065 12.2634 10.0859 12.0325 10.3178C12.0319 10.3184 12.0313 10.319 12.0306 10.3197L10.4906 11.8597C10.0487 12.2968 9.99316 12.9913 10.36 13.493C10.9169 14.2575 11.5333 14.9768 12.2033 15.6444C12.9503 16.3946 13.7623 17.077 14.6299 17.6837C15.1313 18.0334 15.8109 17.9745 16.2446 17.5437L17.7332 16.0317C17.9642 15.7998 18.3394 15.7989 18.5714 16.0299C18.572 16.0305 18.5726 16.0311 18.5732 16.0317L21.2006 18.6637C21.4326 18.8946 21.4334 19.2698 21.2025 19.5018Z" fill="#E6C48E"/>
                                </svg>
                            </a>
                        </li>
                        <li class="list__item list__item_favorite">
                            <a class="list__link" href="<?= $arSettings['header_favourites_link'] ?>">
                                <div class="list__item-icon">
                                    <svg class="icon icon_heart" viewbox="0 0 10 8" style="width: 18px;">
                                        <use xlink:href="#heart" />
                                    </svg>
                                    <span <?=!$arFavourites ? 'style="display: none"' : ''?>>                                        
                                        <?= $arFavourites ? count($arFavourites) : 0?>                                        
                                    </span>
                                </div>
                                <?/*<span><?= $arSettings['header_favourites_name'] ?></span>*/?>
                            </a>
                        </li>

                        <? if (!$isAuthorized) : ?>
                            <li class="list__item">
                                <a class="list__link" href="#login-phone" data-modal>
                                    <svg class="icon icon_person" viewbox="0 0 16 16" style="width: 26px; height: 26px;">
                                        <use xlink:href="#person" />
                                    </svg>
                                    <?/*<span><?= $arSettings['header_personal_name'] ?></span>*/?>
                                </a>
                            </li>
                        <? else : ?>
                            <li class="list__item">
                                <a class="list__link" href="<?= $arSettings['header_personal_link'] ?>">
                                    <? if ($arUser["PERSONAL_PHOTO"]) :
                                        $arUser["PERSONAL_PHOTO"] = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"]["ID"], array('width' => 96, 'height' => 96), BX_RESIZE_IMAGE_EXACT, true);
                                    ?>
                                        <div class="list__item-icon">
                                            <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>" alt="<?= $arUser["NAME"] ?>">
                                        </div>
                                    <? else : ?>
                                        <svg class="icon icon_person" viewbox="0 0 16 16" style="width: 26px; height: 26px;">
                                            <use xlink:href="#person" />
                                        </svg>
                                    <? endif; ?>
                                    <?/*<span><?= $arUser["NAME"] ?? $arSettings['header_personal_name'] ?></span>*/?>
                                </a>
                            </li>
                        <? endif; ?>

                        <? if (intval(Users::getInnerScore()) != 0) { ?>
                            <li class="list__item highlight_orange cert_balance">
                                <span class="list__link">
                                    <?= $arSettings['header_balance_certification'] ?>
                                    <?= number_format(Users::getInnerScore(), 0, '.', ' ') ?>
                                    ₽
                                </span>
                            </li>
                        <? } ?>

                        <?/*<li class="list__item list__item_desktop">
                            <a class="list__link" href="#feedback" data-modal>
                                <svg class="icon icon_message" viewbox="0 0 24 24" style="width: 2.4rem; height: 2.4rem;">
                                    <use xlink:href="#message" />
                                </svg>
                            </a>
                        </li>*/?>
                    </ul>
                </div>
            </div>
        </header>
        <!-- header-->