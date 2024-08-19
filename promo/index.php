<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Промо");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/index.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/promo.css');
$APPLICATION->AddHeadScript('/local/components/naturalist/empty/templates/main/script.js');

use Naturalist\Settings;
use Naturalist\Products;
use Naturalist\Users;

global $arSettings;

// Генерация массива месяцев для фильтра
$currMonthName = FormatDate("f");
$currYear = date('Y');
$nextYear = $currYear + 1;
$arDates = Products::getDates();

// Метеоданные
$coords = (!$isAuthorized) ? $arSettings['main_meteo_coords'] : null;
$arMeteo = Users::getMeteo($coords);

?>

<main class="main">

    <section class="section section_hero">
        <div class="container">
            <div class="hero">
                <img class="hero__name" src="/upload/iblock/a1b/yp6wa960urh02aqkadh1j0yu0al1pmmm.svg" alt="">
                <span class="hero__footnote">БРОНИРОВАНИЕ ГЛЭМПИНГОВ И ЭКО-ОТЕЛЕЙ</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="promo-form" data-bg-form="">
                <form class="form" id="form-main-search">
                    <div class="form__item">
                        <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                            <input type="hidden" data-autocomplete-result>
                            <input class="field__input" type="text" name="name" placeholder="<?= $arSettings['main_search_form_name_placeholder'] ?>" data-autocomplete-field>
                            <div class="autocomplete-dropdown" data-autocomplete-dropdown></div>
                        </div>
                    </div>

                    <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today" data-calendar-max="365">
                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-from><span><?= $arSettings['main_search_form_date_from_span'] ?></span></div>
                            </div>
                        </div>

                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-to><span><?= $arSettings['main_search_form_date_to_span'] ?></span></div>
                            </div>
                        </div>

                        <div class="calendar__dropdown" data-calendar-dropdown="data-calendar-dropdown">
                            <div class="calendar__navigation">
                                <div class="calendar__navigation-item calendar__navigation-item_months">
                                    <div class="calendar__navigation-label" data-calendar-navigation="data-calendar-navigation"><span><?= $currMonthName ?></span></div>
                                    <ul class="list">
                                        <?
                                        $k = 0;
                                        ?>
                                        <? foreach ($arDates[0] as $monthName) : ?>
                                            <li class="list__item<? if ($k == 0) : ?> list__item_active<? endif; ?>">
                                                <button data-calendar-year="<?= $currYear ?>" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
                                            </li>
                                            <? $k++; ?>
                                        <? endforeach ?>
                                        <li class="list__item" data-calendar-delimiter="data-calendar-delimiter">
                                            <div class="list__item-year"><?= $nextYear ?></div>
                                        </li>
                                        <? foreach ($arDates[1] as $monthName) : ?>
                                            <li class="list__item">
                                                <button data-calendar-year="<?= $nextYear ?>" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
                                            </li>
                                            <? $k++; ?>
                                        <? endforeach ?>
                                    </ul>
                                </div>

                                <div class="calendar__navigation-item calendar__navigation-item_years">
                                    <div class="calendar__navigation-label" data-calendar-navigation="data-calendar-navigation"><span><?= $currYear ?></span></div>
                                    <ul class="list">
                                        <li class="list__item list__item_active">
                                            <button data-calendar-year-select="<?= $currYear ?>" type="button"><?= $currYear ?></button>
                                        </li>
                                        <li class="list__item">
                                            <button data-calendar-year-select="<?= $nextYear ?>" type="button"><?= $nextYear ?></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="calendar__month">
                                <input type="hidden" data-calendar-value="data-calendar-value">
                            </div>
                        </div>
                    </div>

                    <div class="form__row">
                        <div class="form__item">
                            <div class="field field_icon guests" data-guests="data-guests">
                                <div class="field__input" data-guests-control="data-guests-control">2 гостя</div>
                                <div class="guests__dropdown">
                                    <div class="guests__guests">
                                        <div class="guests__item">
                                            <div class="guests__label">
                                                <div>Взрослые</div><span>от 18 лет</span>
                                            </div>
                                            <div class="counter">
                                                <button class="counter__minus" type="button"></button>
                                                <input type="text" disabled="disabled" data-guests-adults-count="data-guests-adults-count" name="guests-adults-count" value="2" data-min="1" data-max="99">
                                                <button class="counter__plus" type="button"></button>
                                            </div>
                                        </div>

                                        <div class="guests__item">
                                            <div class="guests__label">
                                                <div>Дети</div><span>от 0 до 17 лет</span>
                                            </div>
                                            <div class="counter">
                                                <button class="counter__minus" type="button"></button>
                                                <input type="text" disabled="disabled" data-guests-children-count="data-guests-children-count" name="guests-children-count" value="0" data-min="0" data-max="20">
                                                <button class="counter__plus" type="button"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="guests__children" data-guests-children="data-guests-children"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form__item">
                            <button class="button button_primary" data-main-search><?= $arSettings['main_search_form_button'] ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="quiz">
        <div data-marquiz-id="65eede6633bbfa00269d9ae5"></div>
        <script>
            (function(t, p) {
                window.Marquiz ? Marquiz.add([t, p]) : document.addEventListener('marquizLoaded', function() {
                    Marquiz.add([t, p])
                })
            })('Inline', {
                id: '65eede6633bbfa00269d9ae5',
                buttonText: 'Начать тест',
                bgColor: '#d34085',
                textColor: '#ffffff',
                rounded: true,
                shadow: 'rgba(211, 64, 133, 0.5)',
                blicked: true
            })
        </script>
    </section>

    <? if ($arMeteo) : ?>
        <section class="section section_info">
            <div class="container">
                <div class="info" data-info-dropdown>
                    <ul class="list">
                        <li class="list__item list__item_large">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/1.svg" alt="N<?= $arMeteo['coords']['lon'] ?>°, E<?= $arMeteo['coords']['lat'] ?>°">
                            <span>N<?= $arMeteo['coords']['lon'] ?>°, E<?= $arMeteo['coords']['lat'] ?>°</span>
                        </li>
                        <li class="list__item">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/2.svg" alt="<?= $arMeteo['sunrise_time'] ?>">
                            <span><?= $arMeteo['sunrise_time'] ?></span>
                        </li>
                        <li class="list__item">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/3.svg" alt="<?= $arMeteo['sunset_time'] ?>">
                            <span><?= $arMeteo['sunset_time'] ?></span>
                        </li>
                        <li class="list__item">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/4.svg" alt="<?= $arMeteo['humidity'] ?>%">
                            <span><?= $arMeteo['humidity'] ?>%</span>
                        </li>
                        <li class="list__item">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/main/info/5.svg" alt="<?= $arMeteo['temp'] ?>">
                            <span><?= $arMeteo['temp'] ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- section-->
    <? endif; ?>

    <section class="promo__seo-text">
        <div class="container">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => '/include/promo-seo-text.php',
                    "EDIT_TEMPLATE" => ""
                )
            );
            ?>            
        </div>
    </section>
    <!--<div class="container">
        <a href="#" class="show-more-seo">Раскрыть</a>
    </div>-->
    <a name="fortune"></a>
    <section class="promo__wheel" id="wheel"></section>

    <section class="section section_offers">
        <div class="container">
            <?
            global $arOffersFilter;
            $arOffersFilter = array();

            $tab = $_REQUEST['tab'] ?? '';
            if ($tab == 'top') {
                $arOffersFilter = array(
                    'UF_TOP' => 1
                );
            }
            if ($tab == 'action') {
                $arOffersFilter = array(
                    '!UF_ACTION' => ''
                );
            }
            if ($tab == 'premium') {
                $arOffersFilter = array(
                    'UF_PREMIUM' => 1
                );
            }

            $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "main_offers",
                array(
                    "VIEW_MODE" => "TEXT",
                    "SHOW_PARENT_NAME" => "Y",
                    "IBLOCK_TYPE" => "",
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_URL" => "",
                    "FILTER_NAME" => "arOffersFilter",
                    "COUNT_ELEMENTS" => "Y",
                    "TOP_DEPTH" => "1",
                    "SECTION_FIELDS" => "",
                    "SECTION_USER_FIELDS" => array("UF_*"),
                    "ADD_SECTIONS_CHAIN" => "Y",
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "36000000",
                    "CACHE_NOTES" => "",
                    "CACHE_GROUPS" => "N",
                    "ITEMS_COUNT" => 12,
                    "TABS" => Settings::getTabsMain(),
                    "SHOW_MORE_LINK" => "/catalog/",
                )
            );
            ?>
        </div>
    </section>
</main>

<script src="https://widgets.risoma.ru/whell/whell_include_js/174/"></script>
<script defer src="<?= SITE_TEMPLATE_PATH ?>/assets/js/index.js?v=<?= filemtime($_SERVER["DOCUMENT_ROOT"] . '/' . SITE_TEMPLATE_PATH . '/assets/js/index.js') ?>"></script>
<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
