<?

use Naturalist\Utils;

foreach ($arResult as $key => $value) {
    ${$key} = $value;
}

use Bitrix\Main\Localization\Loc;
?>
<main class="main">
    <section class="section section_hero">
        <div class="container">
            <div class="hero">
                <img class="hero__name" src="<?= $arSettings['main_hero_name'] ?>" alt>
                <span class="hero__footnote"><?= $arSettings['main_hero_footnote'] ?></span>
            </div>
        </div>
    </section>
    <!-- section-->

    <section class="section section_form">
        <div class="container">
            <div class="main-form" data-bg-form>
                <? if ($arMeteo): ?>
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
                <? endif; ?>

                <?/*div class="h1"><?=$arSettings['main_h1']?></div*/ ?>
                <form class="form" id="form-main-search">
                    <div class="form__item">
                        <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                            <input type="hidden" data-autocomplete-result>
                            <label for="field-place"><?= Loc::getMessage('FILTER_PLACE') ?></label>
                            <input class="field__input" id="field-place" type="text" name="name" placeholder="" data-autocomplete-field>
                            <div class="autocomplete-dropdown-wrap">
                                <div class="autocomplete-dropdown-search">
                                    <input class="field__input" id="field-place" type="text" name="name" placeholder="Регионы или локации" data-autocomplete-field-mobile>
                                </div>
                                <div class="autocomplete-dropdown" data-autocomplete-dropdown>

                                </div>
                                <div class="autocomplete-dropdown-close-wrap">
                                    <div class="autocomplete-dropdown-close">
                                        Закрыть
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today" data-calendar-max="365">
                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <label><?= $arSettings['main_search_form_date_from_span'] ?></label>
                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-from><span></span></div>
                            </div>
                        </div>

                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <label><?= $arSettings['main_search_form_date_to_span'] ?></label>
                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-to><span></span></div>
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

                                        <? foreach ($arDates[1] as $key => $monthName) : ?>

                                            <li class="list__item">
                                                <button data-calendar-year="<?= $nextYear ?>" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
                                                <? if ($key === 0): ?>
                                                    <div class="list__item-year"><?= $nextYear ?></div>
                                                <? endif; ?>
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

                            <div class="calendar__dropdown-close">
                                Закрыть
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
                                                <div><?= GetMessage('FILTER_ADULTS') ?></div><span><?= GetMessage('FILTER_ADULTS_AGE') ?></span>
                                            </div>
                                            <div class="counter">
                                                <button class="counter__minus" type="button"></button>
                                                <input type="text" disabled="disabled" data-guests-adults-count="data-guests-adults-count" name="guests-adults-count" value="2" data-min="1" data-max="99">
                                                <button class="counter__plus" type="button"></button>
                                            </div>
                                        </div>

                                        <div class="guests__item">
                                            <div class="guests__label">
                                                <div><?= GetMessage('FILTER_CHILDREN') ?></div><span><?= GetMessage('FILTER_CHILDREN_AGE') ?></span>
                                            </div>
                                            <div class="counter">
                                                <button class="counter__minus" type="button"></button>
                                                <input type="text" disabled="disabled" data-guests-children-count="data-guests-children-count" name="guests-children-count" value="0" data-min="0" data-max="20">
                                                <button class="counter__plus" type="button"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="guests__children" data-guests-children="data-guests-children"></div>

                                    <div class="guests__dropdown-close">
                                        Закрыть
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form__item">
                            <button class="button" data-main-search>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z" fill="white" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z" fill="white" />
                                </svg>
                                <?= $arSettings['main_search_form_button'] ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?/**/ ?>
        <? if (isset($arHouseTypes) && !empty($arHouseTypes)) { ?>
            <div class="house-type container">
                <div class="house-type__wrapper">
                    <div class="house-type__shadow left"></div>
                    <div class="swiper-container">
                        <ul class="swiper-wrapper">
                            <? foreach ($arHouseTypes as $houseType) {
                                if (!$houseType['UF_IMG']) {
                                    continue;
                                }
                            ?>
                                <li class="swiper-slide">
                                    <a href="<?= $houseType['URL'] ?>">
                                        <?= Utils::buildSVG(CFile::getPath($houseType['UF_IMG'])) ?>
                                        <p class="house-type__text"><?= $houseType['UF_NAME'] ?></p>
                                    </a>
                                </li>
                            <? } ?>
                        </ul>
                    </div>
                    <div class="house-type__shadow right"></div>
                    <div class="swiper-button-prev">
                        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_b_3313_12381)">
                                <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5752 14.0498C17.7949 14.2694 17.7949 14.6256 17.5752 14.8453L13.4205 19L17.5752 23.1548C17.7949 23.3744 17.7949 23.7306 17.5752 23.9503C17.3556 24.1699 16.9994 24.1699 16.7798 23.9503L12.2273 19.3978C12.1218 19.2923 12.0625 19.1492 12.0625 19C12.0625 18.8508 12.1218 18.7078 12.2273 18.6023L16.7798 14.0498C16.9994 13.8301 17.3556 13.8301 17.5752 14.0498Z" fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1899 19C12.1899 18.6893 12.4418 18.4375 12.7524 18.4375H25.3749C25.6856 18.4375 25.9374 18.6893 25.9374 19C25.9374 19.3107 25.6856 19.5625 25.3749 19.5625H12.7524C12.4418 19.5625 12.1899 19.3107 12.1899 19Z" fill="white" />
                            </g>
                            <defs>
                                <filter id="filter0_b_3313_12381" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                    <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_3313_12381" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_3313_12381" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_b_3313_12375)">
                                <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4245 14.0498C20.6442 13.8301 21.0003 13.8301 21.22 14.0498L25.7725 18.6023C25.878 18.7078 25.9373 18.8508 25.9373 19C25.9373 19.1492 25.878 19.2923 25.7725 19.3978L21.22 23.9503C21.0003 24.1699 20.6442 24.1699 20.4245 23.9503C20.2048 23.7306 20.2048 23.3744 20.4245 23.1548L24.5793 19L20.4245 14.8453C20.2048 14.6256 20.2048 14.2694 20.4245 14.0498Z" fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0625 19C12.0625 18.6893 12.3143 18.4375 12.625 18.4375H25.2475C25.5582 18.4375 25.81 18.6893 25.81 19C25.81 19.3107 25.5582 19.5625 25.2475 19.5625H12.625C12.3143 19.5625 12.0625 19.3107 12.0625 19Z" fill="white" />
                            </g>
                            <defs>
                                <filter id="filter0_b_3313_12375" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                    <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_3313_12375" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_3313_12375" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
        <? } ?>
    </section>
    <!-- section-->

    <section class="section section_house">
        <? $APPLICATION->IncludeFile("/include/house-type/house-type.php"); ?>
    </section>

    <? if ($arMeteo): ?>
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
                    "ITEMS_COUNT" => 9,
                    "TABS" => $arTabs,
                    "SHOW_MORE_LINK" => "/catalog/",
                )
            );
            ?>
        </div>
    </section>
    <? $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_slider",
        array(
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "",
            "IBLOCK_ID" => MAIN_SLIDER_IBLOCK_ID,
            "NEWS_COUNT" => "1000",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER2" => "DESC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => [0 => 'DETAIL_PICTURE'],
            "PROPERTY_CODE" => [0 => 'LINK'],
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
            "CACHE_TYPE" => "N",
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
    ); ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_location",
        array(
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "",
            "IBLOCK_ID" => "",
            "NEWS_COUNT" => "1000",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER2" => "DESC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => [],
            "PROPERTY_CODE" => [],
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
    ); ?>
    <section class="why-naturalist">
        <div class="container">
            <div class="why-naturalist__title">
                Почему Натуралист?
            </div>
            <div class="why-naturalist__wrapper">
                <div class="why-naturalist__block">
                    <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block1.php"); ?>
                </div>
                <div class="why-naturalist__block">
                    <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block2.php"); ?>
                </div>
                <div class="why-naturalist__block">
                    <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block3.php"); ?>
                </div>
                <div class="why-naturalist__block">
                    <? $APPLICATION->IncludeFile("/include/why-naturalist/why-block4.php"); ?>
                </div>
            </div>
        </div>
    </section>
    <? $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "impressions_slider",
        array(
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "",
            "IBLOCK_ID" => IMPRESSIONS_IBLOCK_ID,
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
    ); ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:subscribe.form",
        "subscribe-footer",
        array(
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "A",
            "PAGE" => "",
            "SHOW_HIDDEN" => "N",
            "USE_PERSONALIZATION" => "N",
            "COMPONENT_TEMPLATE" => "subscribe-footer",
            "FORM_TITLE" => "Узнавайте первыми о горящих предложениях, новых маршрутах и эксклюзивных скидках",
            "FORM_SUBTITLE" => "Станьте частью Натуралиста и вдохновляйтесь на путешествия вместе с нами",
            "FORM_POLITICS_LINK" => "/policy/"
        ),
        false
    ); ?>
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "smi_us",
        array(
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "AJAX_MODE" => "N",
            "IBLOCK_TYPE" => "",
            "IBLOCK_ID" => SMI_IBLOCK_ID,
            "NEWS_COUNT" => "8",
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER2" => "DESC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => [],
            "PROPERTY_CODE" => ['LINK'],
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
            "FORM" => "Y",
            "NO_TEXT" => true
        )
    );
    ?>
    <section class="cert-index__seo-text">
        <div class="container">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                array(
                    "AREA_FILE_SHOW" => "file",
                    "PATH" => '/include/home-seo-text.php',
                    "EDIT_TEMPLATE" => ""
                )
            );
            ?>
        </div>
    </section>
    <div class="container">
        <a href="#" class="show-more-seo">Раскрыть</a>
    </div>

</main>
<!-- main-->