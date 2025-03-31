<?php

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
    <?php
    $gtagShow = true;
    if (
        stripos($currPage, 'catalog') !== false &&
        count(preg_split('@/@', $currPage, -1, PREG_SPLIT_NO_EMPTY)) == 2
    ) {
        $gtagShow = false;
    }
    if ($gtagShow) {
    ?>
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
    <?php
    }
    ?>

    <meta charset="<?= LANG_CHARSET ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <title><?php $APPLICATION->ShowTitle() ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <?php if ($APPLICATION->GetCurPage() != '/catalog/') { ?>
        <link rel="canonical" href="<?= HTTP_HOST . $APPLICATION->GetCurPage() ?>">
    <?php } ?>

    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />


    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/Montserrat-Bold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/Montserrat-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-Bd.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-DmBd.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-Md.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/TTTravelsNext-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= SITE_TEMPLATE_PATH ?>/assets/fonts/Lato-Regular.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/app.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/app.css'); ?>">
    <?php if (CSite::InDir('/index.php')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/index.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/index.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/catalog')) : ?>
        <?php if ($currPage === "/catalog/" || strpos($currPage, "/catalog/vpechatleniya/") !== false) : ?>
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/catalog.css'); ?>">
        <?php else : ?>
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_history.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_history.css'); ?>">
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/catalog.css'); ?>">
            <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/object.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/object.css'); ?>">
        <?php endif; ?>
    <?php endif; ?>
    <?php if (CSite::InDir('/order')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/reservation.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/reservation.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/personal')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_person.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_person.css'); ?>">
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_person_values.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_person_values.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/personal/reviews')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_reviews.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_reviews.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/personal/active')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_active.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_active.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/personal/history')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_history.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_history.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/personal/favourites')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/lk_favorite.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/lk_favorite.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/about')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/about.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/about.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/impressions')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/impressions.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/impressions.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/map')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/catalog.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/catalog.css'); ?>">
    <?php endif; ?>
    <?php if (CSite::InDir('/objects')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/add_object.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/add_object.css'); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/jquery-ui.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/jquery-ui.min.css'); ?>">
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/custom.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/custom.css'); ?>">

    <?php if (CSite::InDir('/flights')) : ?>
        <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/assets/css/index.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/assets/css/index.css'); ?>">


        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

        <script async src="https://tp.media/content?currency=rub&trs=386385&shmarker=604057&show_hotels=false&powered_by=true&locale=ru&searchUrl=avia.naturalist.travel%2Fflights&primary_override=%2332a8dd&color_button=%23E39250&color_icons=%23F9EED8&dark=%23262626&light=%23FFFFFF&secondary=%23F9EED8&special=%23C4C4C400&color_focused=%2332a8dd&border_radius=0&no_labels=&plain=true&promo_id=7879&campaign_id=100" charset="utf-8"></script>
        <script async src="https://tp.media/content?currency=rub&trs=386385&shmarker=604057&destination=MOW&target_host=www.aviasales.ru%2Fsearch&locale=ru&limit=6&powered_by=true&width=243&primary=%23e69d62&promo_id=4044&campaign_id=100" charset="utf-8"></script>
        <script async src="https://tp.media/content?currency=rub&trs=386385&shmarker=604057&destination=LED&target_host=www.aviasales.ru%2Fsearch&locale=ru&limit=6&powered_by=true&width=243&primary=%23e69d62&promo_id=4044&campaign_id=100" charset="utf-8"></script>
        <script async src="https://tp.media/content?currency=rub&trs=386385&shmarker=604057&destination=VOG&target_host=www.aviasales.ru%2Fsearch&locale=ru&limit=6&powered_by=true&width=243&primary=%23e69d62&promo_id=4044&campaign_id=100" charset="utf-8"></script>
        <script async src="https://tp.media/content?currency=rub&trs=386385&shmarker=604057&destination=KGD&target_host=www.aviasales.ru%2Fsearch&locale=ru&limit=6&powered_by=true&width=243&primary=%23e69d62&promo_id=4044&campaign_id=100" charset="utf-8"></script>
        <script async src="https://tp.media/content?currency=rub&trs=386385&shmarker=604057&lat=&lng=&powered_by=true&search_host=www.aviasales.ru%2Fsearch&locale=ru&value_min=0&value_max=1000000&round_trip=true&only_direct=false&radius=1&draggable=true&disable_zoom=false&show_logo=false&scrollwheel=true&primary=%23E39250&secondary=%233FABDB&light=%23ffffff&width=1212&height=420&zoom=1&promo_id=4054&campaign_id=100" charset="utf-8"></script>
    <?php endif; ?>

    <?php if (CSite::InDir('/promo/')) : ?>
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
    <?php endif; ?>

    <?php $APPLICATION->ShowHead() ?>
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

    <?php
    if (!\Bitrix\Main\Engine\CurrentUser::get()->isAdmin()) {
    ?>

        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function(m, e, t, r, i, k, a) {
                m[i] = m[i] || function() {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                for (var j = 0; j < document.scripts.length; j++) {
                    if (document.scripts[j].src === r) {
                        return;
                    }
                }
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
            })
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(91071014, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true,
                ecommerce: "dataLayer"
            });
        </script>
        <noscript>
            <div><img src="https://mc.yandex.ru/watch/91071014" style="position:absolute; left:-9999px;" alt="" /></div>
        </noscript>
        <!-- /Yandex.Metrika counter -->
    <?php }
    ?>
    <?php /*<script src="https://dmp.one/sync?stock_key=4dce2e8f5fdd1727a46278cb20b97261" async charset="UTF-8"></script>*/ ?>
</head>

<body class="<?php if (CSite::InDir('/map')) : ?>body__on_map<?php endif; ?>">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5762ML9" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->


    <div id="admin_panel"><?php $APPLICATION->ShowPanel(); ?></div>


    <div class="wrapper">
        <header class="header" data-scroll-fixed>
            <div class="container">
                <div class="header__logo">
                    <a class="logotype" href="/"><img src="<?= $arSettings['header_logo'] ?>" alt="<?= $arSettings['header_logo_name'] ?>"></a>
                </div>
                <div class="catalog-filter_close">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.0669 3.93306C16.311 4.17714 16.311 4.57286 16.0669 4.81694L4.81694 16.0669C4.57286 16.311 4.17714 16.311 3.93306 16.0669C3.68898 15.8229 3.68898 15.4271 3.93306 15.1831L15.1831 3.93306C15.4271 3.68898 15.8229 3.68898 16.0669 3.93306Z" fill="black" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.93306 3.93306C4.17714 3.68898 4.57286 3.68898 4.81694 3.93306L16.0669 15.1831C16.311 15.4271 16.311 15.8229 16.0669 16.0669C15.8229 16.311 15.4271 16.311 15.1831 16.0669L3.93306 4.81694C3.68898 4.57286 3.68898 4.17714 3.93306 3.93306Z" fill="black" />
                    </svg>
                </div>
                <button class="header__toggler" data-menu-open type="button">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" rx="8" fill="#E0C695" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 14.1667C12.5 13.8216 12.7798 13.5417 13.125 13.5417L23.125 13.5417C23.4702 13.5417 23.75 13.8216 23.75 14.1667C23.75 14.5119 23.4702 14.7917 23.125 14.7917L13.125 14.7917C12.7798 14.7917 12.5 14.5119 12.5 14.1667Z" fill="black" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 20C12.5 19.6548 12.7798 19.375 13.125 19.375L26.4583 19.375C26.8035 19.375 27.0833 19.6548 27.0833 20C27.0833 20.3452 26.8035 20.625 26.4583 20.625L13.125 20.625C12.7798 20.625 12.5 20.3452 12.5 20Z" fill="black" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5 25.8335C12.5 25.4883 12.7798 25.2085 13.125 25.2085L19.7917 25.2085C20.1368 25.2085 20.4167 25.4883 20.4167 25.8335C20.4167 26.1787 20.1368 26.4585 19.7917 26.4585L13.125 26.4585C12.7798 26.4585 12.5 26.1787 12.5 25.8335Z" fill="black" />
                    </svg>
                </button>
                <div class="header__menu">
                    <nav class="navigation" data-menu>
                        <button class="navigation__close" data-menu-close type="button">
                            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                                <use xlink:href="#cross" />
                            </svg>
                        </button>
                        <ul class="list">
                            <?php
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
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_3293_4462)">
                                        <path d="M3.14786 9.95205C2.35784 8.5745 1.97638 7.44965 1.74637 6.30943C1.40619 4.62306 2.18468 2.97575 3.47432 1.92464C4.01938 1.4804 4.6442 1.63218 4.96651 2.21041L5.69416 3.51584C6.27091 4.55055 6.55929 5.06791 6.50209 5.61641C6.44489 6.16491 6.05598 6.61163 5.27815 7.50509L3.14786 9.95205ZM3.14786 9.95205C4.74693 12.7403 7.25637 15.2511 10.0479 16.8521M10.0479 16.8521C11.4254 17.6421 12.5503 18.0235 13.6905 18.2536C15.3769 18.5937 17.0242 17.8152 18.0753 16.5256C18.5195 15.9805 18.3677 15.3557 17.7895 15.0334L16.4841 14.3058C15.4494 13.729 14.932 13.4406 14.3835 13.4978C13.835 13.555 13.3883 13.9439 12.4948 14.7218L10.0479 16.8521Z" stroke="black" stroke-width="1.5" stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_3293_4462">
                                            <rect width="20" height="20" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                        </li>
                        <? if (number_format(Users::getInnerScore(), 0, '.', ' ') > 0): ?>
                            <li class="list__item list__item_cert">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.666 8.83337C11.9422 8.83337 12.166 9.05723 12.166 9.33337C12.166 9.60952 11.9422 9.83337 11.666 9.83337C11.3899 9.83337 11.166 9.60952 11.166 9.33337C11.166 9.05723 11.3899 8.83337 11.666 8.83337ZM13.166 9.33337C13.166 8.50495 12.4944 7.83337 11.666 7.83337C10.8376 7.83337 10.166 8.50495 10.166 9.33337C10.166 10.1618 10.8376 10.8334 11.666 10.8334C12.4944 10.8334 13.166 10.1618 13.166 9.33337Z" fill="black" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3879 2.55114C10.2132 2.50433 9.98518 2.50003 9.33301 2.50003H6.66634C5.39513 2.50003 4.49202 2.50109 3.80691 2.59321C3.13618 2.68338 2.74975 2.8525 2.46761 3.13463C2.18547 3.41677 2.01636 3.80321 1.92618 4.47393C1.83407 5.15904 1.83301 6.06215 1.83301 7.33337V8.6667C1.83301 9.93791 1.83407 10.841 1.92618 11.5261C2.01636 12.1969 2.18547 12.5833 2.46761 12.8654C2.74975 13.1476 3.13618 13.3167 3.80691 13.4069C4.49202 13.499 5.39513 13.5 6.66634 13.5H10.6663C11.6233 13.5 12.2907 13.499 12.7943 13.4313C13.2834 13.3655 13.5425 13.2452 13.727 13.0607C13.9115 12.8762 14.0318 12.6171 14.0976 12.128C14.1653 11.6244 14.1663 10.957 14.1663 10V8.6667C14.1663 7.70976 14.1653 7.04235 14.0976 6.53878C14.0318 6.0496 13.9115 5.79055 13.727 5.60604C13.5425 5.42153 13.2834 5.30123 12.7943 5.23546C12.2907 5.16776 11.6233 5.1667 10.6663 5.1667H6.66634C6.3902 5.1667 6.16634 4.94284 6.16634 4.6667C6.16634 4.39056 6.3902 4.1667 6.66634 4.1667L10.7029 4.1667C10.9844 4.1667 11.2489 4.16669 11.4973 4.16897C11.4929 3.87747 11.4808 3.73203 11.4486 3.6118C11.3099 3.09417 10.9055 2.68984 10.3879 2.55114ZM12.4984 4.20179C12.4943 3.85666 12.478 3.59018 12.4145 3.35299C12.1833 2.49026 11.5095 1.81639 10.6467 1.58522C10.3276 1.49971 9.95551 1.49984 9.41021 1.50002C9.38485 1.50002 9.35912 1.50003 9.33301 1.50003L6.62873 1.50003C5.40356 1.50002 4.43314 1.50001 3.67366 1.60212C2.89205 1.70721 2.25941 1.92862 1.7605 2.42753C1.26159 2.92644 1.04018 3.55907 0.935098 4.34068C0.832989 5.10016 0.832997 6.07058 0.833008 7.29575V8.70431C0.832997 9.92948 0.832989 10.8999 0.935098 11.6594C1.04018 12.441 1.26159 13.0736 1.7605 13.5725C2.25941 14.0714 2.89204 14.2929 3.67366 14.3979C4.43314 14.5001 5.40356 14.5 6.62873 14.5H10.7029C11.6146 14.5 12.3495 14.5001 12.9275 14.4224C13.5276 14.3417 14.0328 14.1691 14.4341 13.7678C14.8354 13.3665 15.008 12.8613 15.0887 12.2612C15.1664 11.6832 15.1664 10.9483 15.1663 10.0366V8.63012C15.1664 7.71839 15.1664 6.98351 15.0887 6.40553C15.008 5.80546 14.8354 5.30021 14.4341 4.89893C14.0328 4.49766 13.5276 4.32506 12.9275 4.24438C12.793 4.2263 12.6501 4.21243 12.4984 4.20179Z" fill="black" />
                                </svg>
                                <span><?= number_format(Users::getInnerScore(), 0, '.', ' ') ?> ₽ </span>
                            </li>
                        <? endif; ?>
                        <li class="list__item list__item_favorite">
                            <a class="list__link" href="<?= $arSettings['header_favourites_link'] ?>">
                                <div class="list__item-icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.45441 2.79572C5.93855 1.27194 8.16707 1.87923 9.51335 2.89026C9.73484 3.05659 9.88684 3.17042 10 3.24729C10.1132 3.17042 10.2652 3.05659 10.4866 2.89026C11.8329 1.87923 14.0615 1.27194 16.5456 2.79572C18.2633 3.84936 19.2298 6.05026 18.8907 8.57902C18.5499 11.12 16.9052 13.9938 13.4222 16.5718C13.3931 16.5934 13.3642 16.6148 13.3355 16.636C12.126 17.5318 11.3251 18.125 10 18.125C8.67495 18.125 7.87396 17.5318 6.66447 16.636C6.63581 16.6148 6.60691 16.5934 6.57778 16.5718C3.09478 13.9938 1.45012 11.12 1.10933 8.57902C0.770179 6.05026 1.7367 3.84936 3.45441 2.79572ZM9.86938 3.32783C9.8667 3.32904 9.86718 3.32865 9.87054 3.3273C9.87011 3.3275 9.86972 3.32768 9.86938 3.32783ZM10.1295 3.32729C10.1328 3.32864 10.1333 3.32904 10.1306 3.32783C10.1303 3.32768 10.1299 3.3275 10.1295 3.32729ZM8.76273 3.88979C7.76562 3.14099 6.09326 2.64347 4.10799 3.86123C2.89289 4.60658 2.0612 6.27264 2.34824 8.41286C2.63365 10.5409 4.0396 13.138 7.32144 15.5671C8.63991 16.543 9.12114 16.875 10 16.875C10.8789 16.875 11.3601 16.543 12.6786 15.5671C15.9604 13.138 17.3664 10.5409 17.6518 8.41286C17.9388 6.27264 17.1071 4.60658 15.892 3.86123C13.9067 2.64347 12.2344 3.14099 11.2373 3.88979L11.2198 3.90288C10.9945 4.0721 10.801 4.21746 10.6462 4.31882C10.5657 4.3715 10.4766 4.42514 10.3841 4.46693C10.2963 4.50659 10.1615 4.55621 10 4.55621C9.83847 4.55621 9.70365 4.50659 9.61589 4.46693C9.52341 4.42514 9.43431 4.3715 9.35384 4.31882C9.19901 4.21745 9.00546 4.07209 8.78012 3.90285L8.76273 3.88979Z" fill="black" />
                                    </svg>
                                    <span <?= !$arFavourites ? 'style="display: none"' : '' ?>>
                                        <?= $arFavourites ? count($arFavourites) : 0 ?>
                                    </span>
                                </div>
                                <?php /*<span><?= $arSettings['header_favourites_name'] ?></span>*/ ?>
                            </a>
                        </li>

                        <?php if (!$isAuthorized) : ?>
                            <li class="list__item list__item_login">
                                <a class="list__link" href="#login-phone" data-modal><?= GetMessage('LOGIN_HEADER') ?>
                                    <?/*svg class="icon icon_person" viewbox="0 0 16 16" style="width: 26px; height: 26px;">
                                        <use xlink:href="#person" />
                                    </svg*/ ?>
                                    <?php /*<span><?= $arSettings['header_personal_name'] ?></span>*/ ?>

                                </a>
                            </li>
                        <?php else : ?>
                            <li class="list__item list__item_login">
                                <a class="list__link is-authorized" href="<?= $arSettings['header_personal_link'] ?>">
                                    <?php /* if ($arUser["PERSONAL_PHOTO"]) :
                                        $arUser["PERSONAL_PHOTO"] = CFile::ResizeImageGet($arUser["PERSONAL_PHOTO"]["ID"], array('width' => 96, 'height' => 96), BX_RESIZE_IMAGE_EXACT, true);
                                    ?>
                                        <div class="list__item-icon">
                                            <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>" alt="<?= $arUser["NAME"] ?>">
                                        </div>
                                    <? else : */ ?>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.1991 13.4384C11.6314 11.9095 8.36886 11.9095 5.80122 13.4384C5.6613 13.5217 5.50798 13.6087 5.34738 13.6998C4.75355 14.0367 4.06019 14.4301 3.57679 14.9032C3.27668 15.197 3.14875 15.4393 3.12829 15.6265C3.11204 15.7752 3.14962 16.0203 3.52514 16.378C4.38835 17.2004 5.2655 17.7084 6.32587 17.7084H13.6744C14.7348 17.7084 15.6119 17.2004 16.4751 16.378C16.8507 16.0203 16.8883 15.7752 16.872 15.6265C16.8515 15.4393 16.7236 15.197 16.4235 14.9032C15.9401 14.4301 15.2467 14.0367 14.6529 13.6998C14.4923 13.6087 14.339 13.5217 14.1991 13.4384ZM14.8386 12.3644L14.5188 12.9014L14.8386 12.3644C14.9355 12.4221 15.0553 12.4897 15.1908 12.5661C15.7848 12.9013 16.6827 13.4078 17.2979 14.0099C17.6826 14.3865 18.0481 14.8828 18.1146 15.4907C18.1853 16.1373 17.9032 16.744 17.3374 17.2831C16.3612 18.2131 15.1897 18.9584 13.6744 18.9584H6.32587C4.81061 18.9584 3.63913 18.2131 2.66292 17.2831C2.09707 16.744 1.81502 16.1373 1.88569 15.4907C1.95215 14.8828 2.31769 14.3865 2.70241 14.0099C3.31755 13.4078 4.21548 12.9013 4.80949 12.5661C4.94504 12.4897 5.06477 12.4221 5.1617 12.3644C8.12341 10.6009 11.8769 10.6009 14.8386 12.3644Z" fill="black" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.29175C8.27411 2.29175 6.875 3.69086 6.875 5.41675C6.875 7.14264 8.27411 8.54175 10 8.54175C11.7259 8.54175 13.125 7.14264 13.125 5.41675C13.125 3.69086 11.7259 2.29175 10 2.29175ZM5.625 5.41675C5.625 3.0005 7.58375 1.04175 10 1.04175C12.4162 1.04175 14.375 3.0005 14.375 5.41675C14.375 7.83299 12.4162 9.79175 10 9.79175C7.58375 9.79175 5.625 7.83299 5.625 5.41675Z" fill="black" />
                                    </svg>
                                    <?php // endif;
                                    ?>
                                    <?php /*<span><?= $arUser["NAME"] ?? $arSettings['header_personal_name'] ?></span>*/ ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?/* if (intval(Users::getInnerScore()) != 0) { ?>
                            <li class="list__item highlight_orange cert_balance">
                                <span class="list__link">
                                    <?= $arSettings['header_balance_certification'] ?>
                                    <?= number_format(Users::getInnerScore(), 0, '.', ' ') ?>
                                    ₽
                                </span>
                            </li>
                        <? } */ ?>

                        <?/*<li class="list__item list__item_desktop">
                            <a class="list__link" href="#feedback" data-modal>
                                <svg class="icon icon_message" viewbox="0 0 24 24" style="width: 2.4rem; height: 2.4rem;">
                                    <use xlink:href="#message" />
                                </svg>
                            </a>
                        </li>*/ ?>
                    </ul>
                </div>
            </div>
        </header>
        <!-- header-->