<?
foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<main class="main">
    <section class="section section_hero">
        <div class="container">
            <div class="hero">
                <img class="hero__name" src="<?=$arSettings['main_hero_name']?>" alt>
                <span class="hero__footnote"><?=$arSettings['main_hero_footnote']?></span>
            </div>
        </div>
    </section>
    <!-- section-->

    <section class="section section_form">
        <div class="clip">
            <img class="clip__bg" src="<?=$srcMainBg?>" alt>
            <svg width="0" height="0" style="display: block;">
                <defs>
                    <clippath id="clip-circles" />
                </defs>
            </svg>
            <div class="clip__delimiter" data-bg-delimiter></div>
            <div class="clip__loader" data-loader></div>
        </div>

        <div class="container">
            <div class="main-form" data-bg-form>
                <?if($arMeteo):?>
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
                <?endif;?>

                <div class="h1"><?=$arSettings['main_h1']?></div>
                <form class="form" id="form-main-search">
                    <div class="form__item">
                        <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                            <input type="hidden" data-autocomplete-result>
                            <input class="field__input" type="text" name="name" placeholder="<?=$arSettings['main_search_form_name_placeholder']?>" data-autocomplete-field>
                            <div class="autocomplete-dropdown" data-autocomplete-dropdown></div>
                        </div>
                    </div>

                    <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today" data-calendar-max="365">
                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-from><span><?=$arSettings['main_search_form_date_from_span']?></span></div>
                            </div>
                        </div>

                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-to><span><?=$arSettings['main_search_form_date_to_span']?></span></div>
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
                                                <div>Дети</div>
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
                            <button class="button button_primary" data-main-search><?=$arSettings['main_search_form_button']?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- section-->

    <?if($arMeteo):?>
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
    <?endif;?>

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
            // Заезд, выезд, кол-во гостей
            /*
            if (!empty($_GET['dateFrom']) && !empty($_GET['dateTo']) && !empty($_GET['guests'])) {
                $dateFrom = $_GET['dateFrom'];
                $dateTo = $_GET['dateTo'];
                $guests = $_GET['guests'];

                // Запрос в апи на получение списка кемпингов со свободными местами в выбранный промежуток
                $arExternalIDs = Products::search($guests, $dateFrom, $dateTo, false);
                if($arExternalIDs) {
                    $arOffersFilter["UF_EXTERNAL_ID"] = $arExternalIDs;
                } else {
                    $arOffersFilter["UF_EXTERNAL_ID"] = false;
                }
            }
            */

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
                    "TABS" => $arTabs,
                    "SHOW_MORE_LINK" => "/catalog/"
                )
            );
            ?>
        </div>
    </section>
    <!-- section-->
    <?/*
    <section class="section section_news">
        <div class="container news-preview">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "main_news",
                array(
                    "DISPLAY_DATE" => "Y",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "Y",
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "",
                    "IBLOCK_ID" => NEWS_IBLOCK_ID,
                    "NEWS_COUNT" => "1000",
                    "SORT_BY1" => "SORT",
                    "SORT_ORDER1" => "ASC",
                    "SORT_BY2" => "ACTIVE_FROM",
                    "SORT_ORDER2" => "DESC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => array("ID"),
                    "PROPERTY_CODE" => array("LINK", ""),
                    "CHECK_DATES" => "Y",
                    "DETAIL_URL" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "SET_TITLE" => "N",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
                    "PARENT_SECTION" => "",
                    "PARENT_SECTION_CODE" => "",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "N",
                    "DISPLAY_TOP_PAGER" => "Y",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "Y",
                    "PAGER_TEMPLATE" => "",
                    "PAGER_DESC_NUMBERING" => "Y",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "Y",
                    "PAGER_BASE_LINK_ENABLE" => "Y",
                    "SET_STATUS_404" => "N",
                    "SHOW_404" => "N",
                    "MESSAGE_404" => "",
                    "PAGER_BASE_LINK" => "",
                    "PAGER_PARAMS_NAME" => "arrPager",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                )
            );
            ?>
        </div>
    </section>
    */?>
    <!-- section-->
</main>
<!-- main-->