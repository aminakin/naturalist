<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


use Bitrix\Iblock\Elements\ElementGlampingsTable;
use Naturalist\Utils;

use Bitrix\Main\Localization\Loc;



$isSeoText = false;

$arSections = $arResult['SECTIONS'];



$allCount = count($arSections);



/* Пагинация */
$page = $_REQUEST['page'] ?? 1;

$pageCount = ceil(count($arSections) / $arParams["ITEMS_COUNT"]);
if ($pageCount > 1) {
    $arPageSections = array_slice($arSections, ($page - 1) * $arParams["ITEMS_COUNT"], $arParams["ITEMS_COUNT"]);
} else {
    $arPageSections = $arSections;
}

// Добавляем свойство Скидка, если есть хотя бы 1 элемент со скидкой
foreach ($arPageSections as $section) {
    $arSectionIds[] = $section['ID'];
}
unset($section);

$elements = ElementGlampingsTable::getList([
    'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
    'filter' => ['IBLOCK_SECTION_ID' => $arSectionIds],
])->fetchAll();

foreach ($elements as $element) {
    $arElementsBySection[$element['IBLOCK_SECTION_ID']][] = $element;
}
unset($element);

foreach ($arPageSections as &$section) {
    foreach ($arElementsBySection[$section['ID']] as $element) {
        $arPrice = CCatalogProduct::GetOptimalPrice($element['ID'], 1, $USER->GetUserGroupArray(), 'N');
        if (is_array($arPrice['DISCOUNT']) && count($arPrice['DISCOUNT'])) {
            $section['IS_DISCOUNT'] = 'Y';
            $section['DISCOUNT_PERCENT'] = $arPrice['RESULT_PRICE']['PERCENT'];
            break;
        }
    }
}
unset($section);

/* Генерация массива месяцев для фильтра */
$arDates = array();
$currMonth = date('m');
$currMonthName = FormatDate("f");
$currYear = date('Y');
$nextYear = $currYear + 1;
for ($i = $currMonth; $i <= 12; $i++) {
    $arDates[0][] = FormatDate("f", strtotime('1970-' . $i . '-01'));
}
for ($j = 1; $j <= 12; $j++) {
    $arDates[1][] = FormatDate("f", strtotime('1970-' . $j . '-01'));
}

if ($arResult['pageSeoData']) {
    if ($arResult['pageSeoData']['UF_H1']) {
        $APPLICATION->SetPageProperty("custom_title", $arResult['pageSeoData']['UF_H1']);
    }
    if ($arResult['pageSeoData']['UF_TITLE']) {
        $APPLICATION->SetTitle($arResult['pageSeoData']['UF_TITLE']);
    }
    if ($arResult['pageSeoData']['UF_DESCRIPTION']) {
        $APPLICATION->SetPageProperty("description", $arResult['pageSeoData']['UF_DESCRIPTION']);
    }
} else {
    $APPLICATION->SetTitle($arResult['titleSEO']);
    $APPLICATION->SetPageProperty("custom_title", $arResult['h1SEO']);
    $APPLICATION->SetPageProperty("description", $arResult['descriptionSEO']);
}

if (!count($arPageSections)) {
    $APPLICATION->AddHeadString('<meta name="robots" content="noindex">', true);
}

if (empty($arResult['CHPY'])) {
    $APPLICATION->AddHeadString('<link rel="canonical" href="' . HTTP_HOST . $APPLICATION->GetCurPage() . '">', true);
}

if ($arResult['CHPY']['UF_CANONICAL']) {
    $APPLICATION->AddHeadString('<link rel="canonical" href="' . HTTP_HOST . $arResult['CHPY']['UF_CANONICAL'] . '">', true);
} ?>


<? if ($APPLICATION->GetCurPage() == '/catalog/'): ?>
    <div class="catalog__full">
        <div class="catalog-full__wrapper">
        <? endif; ?>
        <main class="main main__on_map">
            <section class="section section_crumbs section_crumbs_catalog_new">
                <div class="container">
                    <?
                    $APPLICATION->IncludeComponent(
                        "naturalist:empty",
                        "catalog_breadcrumbs",
                        array(
                            "map" => $arParams["MAP"]
                        )
                    );
                    ?>
                    <div class="wrapper_title_catalog_page">
                        <h1 class="page_title"><? $APPLICATION->ShowProperty("custom_title") ?></h1>
                    </div>
                    <div class="fake-filter_catalog">
                        <div class="fake-filter_inputs">
                            <div class="fake-filter_location">
                                <?= ($arResult['SECTION_FILTER_VALUES']["SEARCH_TEXT"]) ? $arResult['SECTION_FILTER_VALUES']["SEARCH_TEXT"] :  Loc::getMessage('FILTER_PLACE') ?>
                            </div>
                            <div class="fake-filter_date">
                                <span class="from"><?= ($arResult['arUriParams']['dateFrom']) ? $arResult['arUriParams']['dateFrom'] : 'Заезд' ?></span>
                                &nbsp;-&nbsp;
                                <span class="to"><?= ($arResult['arUriParams']['dateTo']) ? $arResult['arUriParams']['dateTo'] : 'Выезд' ?></span>
                                <span class="fake-filter_guest"><?= $arResult['arUriParams']['guests'] ? '<span class="dot"></span>' . $arResult['arUriParams']['guests'] + $arResult['arUriParams']['children'] . ' ' . $arResult['guestsDeclension']->get($arResult['arUriParams']['guests'] + $arResult['arUriParams']['children']) : '' ?></span>
                            </div>
                        </div>
                        <div class="fake-filter_btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z" fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z" fill="white" />
                            </svg>
                        </div>
                    </div>
                    <div class="catalog_filter catalog_map">
                        <form class="form filters" id="form-catalog-filter-front">
                            <div class="form_group_wrapper">
                                <div class="form__item item_name">
                                    <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                                        <input type="hidden" data-autocomplete-result value='<?= ($arResult['SECTION_FILTER_VALUES']["SEARCH"]) ? $arResult['SECTION_FILTER_VALUES']["SEARCH"] : null ?>'>
                                        <label for="field-place"><?= Loc::getMessage('FILTER_PLACE') ?></label>
                                        <input class="field__input" type="text" name="name" placeholder="" data-autocomplete-field value='<?= ($arResult['SECTION_FILTER_VALUES']["SEARCH_TEXT"]) ? $arResult['SECTION_FILTER_VALUES']["SEARCH_TEXT"] : null ?>'>
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
                                <div class="form_group_wrapper-filter_items">
                                    <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today" data-calendar-max="365">
                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <label><?= Loc::getMessage('FILTER_FROM') ?></label>
                                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-from><?= ($arResult['arUriParams']['dateFrom']) ? $arResult['arUriParams']['dateFrom'] : '<span></span>' ?></div>
                                            </div>
                                        </div>
                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <label><?= Loc::getMessage('FILTER_TO') ?></label>
                                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-to><?= ($arResult['arUriParams']['dateTo']) ? $arResult['arUriParams']['dateTo'] : '<span></span>' ?>
                                                </div>
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
                                    <div class="form__item guest">
                                        <div class="field field_icon guests" data-guests="data-guests">
                                            <div class="field__input" data-guests-control="data-guests-control"><?= $arResult['arUriParams']['guests'] + $arResult['arUriParams']['children'] ?> <?= $arResult['guestsDeclension']->get($arResult['arUriParams']['guests'] + $arResult['arUriParams']['children']) ?></div>
                                            <div class="guests__dropdown">
                                                <div class="guests__guests">
                                                    <div class="guests__item">
                                                        <div class="guests__label">
                                                            <div><?= GetMessage('FILTER_ADULTS') ?></div>
                                                            <span><?= GetMessage('FILTER_ADULTS_AGE') ?></span>
                                                        </div>
                                                        <div class="counter">
                                                            <button class="counter__minus" type="button"></button>
                                                            <input type="text" disabled="disabled"
                                                                data-guests-adults-count="data-guests-adults-count"
                                                                name="guests-adults-count" value="<?= $arResult['arUriParams']['guests'] ?>"
                                                                data-min="1">
                                                            <button class="counter__plus" type="button"></button>
                                                        </div>
                                                    </div>
                                                    <div class="guests__item">
                                                        <div class="guests__label">
                                                            <div><?= GetMessage('FILTER_CHILDREN') ?></div>
                                                            <span><?= GetMessage('FILTER_CHILDREN_AGE') ?></span>
                                                        </div>
                                                        <div class="counter">
                                                            <button class="counter__minus" type="button"></button>
                                                            <input type="text" disabled="disabled"
                                                                data-guests-children-count="data-guests-children-count"
                                                                name="guests-children-count" value="<?= $arResult['arUriParams']['children'] ?>"
                                                                data-min="0">
                                                            <button class="counter__plus" type="button"></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="guests__children"
                                                    data-guests-children="data-guests-children">
                                                    <?php if ($arResult['arUriParams']['childrenAge']): ?>
                                                        <?php foreach ($arResult['arUriParams']['childrenAge'] as $keyAge => $valueAge): ?>
                                                            <div class="guests__item">
                                                                <div class="guests__label">
                                                                    <div><?= GetMessage('FILTER_CHILD_AGE') ?></div>
                                                                    <span><?= getChildrenOrderTitle($keyAge + 1) ?> <?= GetMessage('FILTER_CHILD') ?></span>
                                                                </div>
                                                                <div class="counter">
                                                                    <button class="counter__minus" type="button">
                                                                        <svg class="icon icon_arrow-small"
                                                                            viewBox="0 0 16 16"
                                                                            style="width: 1.6rem; height: 1.6rem;">
                                                                            <use xlink:href="#arrow-small"></use>
                                                                        </svg>
                                                                    </button>
                                                                    <input type="text" disabled=""
                                                                        data-guests-children=""
                                                                        name="guests-children-<?= $keyAge ?>"
                                                                        value="<?= $valueAge ?>"
                                                                        data-min="0" data-max="17">
                                                                    <button class="counter__plus" type="button">
                                                                        <svg class="icon icon_arrow-small"
                                                                            viewBox="0 0 16 16"
                                                                            style="width: 1.6rem; height: 1.6rem;">
                                                                            <use xlink:href="#arrow-small"></use>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="guests__dropdown-close">
                                                    Закрыть
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="filters__controls">
                                <button class="button button_primary" data-filter-set data-filter-catalog-front-btn="true">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z" fill="white" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z" fill="white" />
                                    </svg>
                                    <span><?= Loc::getMessage('FILTER_SEARCH') ?></span>
                                </button>
                            </div>
                        </form>
                        <button class="button button-clear" data-filter-reset><?= Loc::getMessage('FILTER_RESET') ?></button>
                    </div>
                    <? if ($APPLICATION->GetCurPage() == '/catalog/'): ?>
                        <? if (isset($arResult['houseTypeData']) && !empty($arResult['houseTypeData'])): ?>
                            <div class="house-type container">
                                <div class="house-type__wrapper">
                                    <div class="house-type__shadow left"></div>
                                    <div class="swiper-container">
                                        <ul class="swiper-wrapper">
                                            <? foreach ($arResult['houseTypeData'] as $houseType) {
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
                                            <? }
                                            unset($houseType);
                                            ?>
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
                        <? endif; ?>
                    <? endif; ?>
                    <div class="catalog_sorter<?= (CSite::InDir('/map') ? ' map' : '') ?>">
                        <div class="filter_btn">
                            <?php if (CSite::InDir('/map')): ?>
                                <a href="/catalog/<?= ($_SERVER['QUERY_STRING'] !== '') ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="button button_primary catalog__map-halfscreen link__to_catalog">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.942 2.95796C13.1861 3.20204 13.1861 3.59777 12.942 3.84184L7.50864 9.27518C7.11105 9.67277 7.11105 10.327 7.50864 10.7246L12.942 16.158C13.1861 16.402 13.1861 16.7978 12.942 17.0418C12.6979 17.2859 12.3022 17.2859 12.0581 17.0418L6.62476 11.6085C5.73901 10.7228 5.73901 9.27704 6.62476 8.39129L12.0581 2.95796C12.3022 2.71388 12.6979 2.71388 12.942 2.95796Z" fill="black" />
                                    </svg>
                                    <span>Каталог</span>
                                </a>
                            <?php endif; ?>
                            <a class="button filter" href="#filters-modal" data-modal="data-modal">
                                <span>Фильтры</span> <?= ($arResult['FILTER_COUNT'] !== 0) ? '<span class="filter-count">' . $arResult['FILTER_COUNT'] . '</span>' : '' ?>
                            </a>
                            <div class="price-filter__wrap">
                                <a class="button price" href="#">
                                    <span>Цена</span>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.6464 12.9799L5.8535 9.18705C5.53852 8.87207 5.7616 8.3335 6.20706 8.3335H13.7928C14.2383 8.3335 14.4614 8.87207 14.1464 9.18705L10.3535 12.9799C10.1582 13.1752 9.84166 13.1752 9.6464 12.9799Z" fill="black" />
                                    </svg>
                                </a>
                                <div class="price-filter">
                                    <div class="price-filter__title">Цена</div>
                                    <div class="price-inputs__wrap">
                                        <div class="price-input__wrap">
                                            <label for="min-price">от</label>
                                            <input type="text" class="min-price" data-price-value="<?= $arResult['minPrice'] ?>" name="min-price" value="<?= $arResult['minPrice'] ?>" size="5">
                                            <span>&nbsp;₽</span>
                                        </div>
                                        <div class="price-input__wrap">
                                            <label for="max-price">до</label>
                                            <input type="text" class="max-price" data-price-value="<?= $arResult['maxPrice'] ?>" name="max-price" value="<?= $arResult['maxPrice'] ?>" size="5">
                                            <span>&nbsp;₽</span>
                                        </div>
                                    </div>
                                    <div class="slider-range"></div>

                                    <div class="price-filter__controls">
                                        <button class="button button-clear" data-filter-reset>Сбросить</button>
                                        <button class="button button_primary button-accept" data-filter-set>Применить</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!CSite::InDir('/map')): ?>
                            <div class="sort__wrapper">
                                <div class="sort__btn">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.50837 17.2086C7.49068 17.2086 7.47516 17.2053 7.46184 17.2C7.44898 17.1948 7.43493 17.1864 7.42025 17.1717L3.24525 12.9967C3.22373 12.9751 3.21045 12.9444 3.21045 12.9086C3.21045 12.8727 3.22373 12.842 3.24525 12.8204C3.26678 12.7989 3.29751 12.7856 3.33337 12.7856C3.36922 12.7856 3.39995 12.7989 3.42148 12.8204L7.59648 16.9954C7.61801 17.017 7.63128 17.0477 7.63128 17.0836C7.63128 17.1165 7.62006 17.1452 7.60152 17.1663C7.56161 17.2006 7.5255 17.2086 7.50837 17.2086Z" fill="black" stroke="black" />
                                        <path d="M7.5083 17.2082C7.47815 17.2082 7.44687 17.1959 7.42123 17.1702C7.39559 17.1446 7.3833 17.1133 7.3833 17.0832V2.9165C7.3833 2.88636 7.39559 2.85508 7.42123 2.82943C7.44687 2.80379 7.47815 2.7915 7.5083 2.7915C7.53845 2.7915 7.56973 2.80379 7.59537 2.82943C7.62102 2.85508 7.6333 2.88636 7.6333 2.9165V17.0832C7.6333 17.1133 7.62102 17.1446 7.59537 17.1702C7.56973 17.1959 7.53845 17.2082 7.5083 17.2082Z" fill="black" stroke="black" />
                                        <path d="M16.6751 7.21686C16.6574 7.21686 16.6419 7.21364 16.6286 7.20828C16.6157 7.2031 16.6017 7.19466 16.587 7.17997L12.412 3.00498C12.3905 2.98345 12.3772 2.95271 12.3772 2.91686C12.3772 2.88101 12.3905 2.85028 12.412 2.82875C12.4335 2.80722 12.4643 2.79395 12.5001 2.79395C12.536 2.79395 12.5667 2.80722 12.5882 2.82875L16.7632 7.00375C16.7848 7.02528 16.798 7.05601 16.798 7.09186C16.798 7.12771 16.7848 7.15845 16.7632 7.17997C16.7485 7.19466 16.7345 7.2031 16.7216 7.20828C16.7083 7.21364 16.6928 7.21686 16.6751 7.21686Z" fill="black" stroke="black" />
                                        <path d="M12.4917 17.2082C12.4616 17.2082 12.4303 17.1959 12.4046 17.1702C12.379 17.1446 12.3667 17.1133 12.3667 17.0832V2.9165C12.3667 2.88636 12.379 2.85508 12.4046 2.82943C12.4303 2.80379 12.4616 2.7915 12.4917 2.7915C12.5218 2.7915 12.5531 2.80379 12.5788 2.82943C12.6044 2.85508 12.6167 2.88636 12.6167 2.9165V17.0832C12.6167 17.1531 12.5612 17.2082 12.4917 17.2082Z" fill="black" stroke="black" />
                                    </svg>
                                    <span>Сортировка</span>
                                </div>
                                <ul class="sort__list">
                                    <li class="list__item">
                                        <?php if ($arResult['arSort'] == "popular"): ?>
                                            <span class="list__link" data-sort="popular" data-type="<?= $arResult['orderReverse'] ?>">
                                                <span>Популярные</span>
                                                <input type="radio" id="radio-1" checked>
                                                <label for="radio-1"></label>
                                            </span>
                                        <?php else: ?>
                                            <a class="list__link" href="#" data-sort="popular" data-type="asc">
                                                <span>Популярные</span>
                                                <input type="radio" id="radio-1">
                                                <label for="radio-1"></label>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                    <li class="list__item">
                                        <?php if ($arResult['arSort'] == "price" && $_GET['order'] == 'asc'): ?>
                                            <span class="list__link" data-sort="price" data-type="<?= $arResult['orderReverse'] ?>">
                                                <span>Сначала дешевле</span>
                                                <input type="radio" id="radio-2" checked>
                                                <label for="radio-2"></label>
                                            </span>
                                        <?php else: ?>
                                            <a class="list__link" href="#" data-sort="price" data-type="asc">
                                                <span>Сначала дешевле</span>
                                                <input type="radio" id="radio-2">
                                                <label for="radio-2"></label>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                    <li class="list__item">
                                        <?php if ($arResult['arSort'] == "price" && $_GET['order'] == 'desc'): ?>
                                            <span class="list__link" data-sort="price" data-type="<?= $arResult['orderReverse'] ?>">
                                                <span>Сначала дороже</span>
                                                <input type="radio" id="radio-3" checked>
                                                <label for="radio-3"></label>
                                            </span>
                                        <?php else: ?>
                                            <a class="list__link" href="#" data-sort="price" data-type="desc">
                                                <span>Сначала дороже</span>
                                                <input type="radio" id="radio-3">
                                                <label for="radio-3"></label>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                    <li class="list__item">
                                        <?php if ($arResult['arSort'] == "rating"): ?>
                                            <span class="list__link" data-sort="rating" data-type="<?= $arResult['orderReverse'] ?>">
                                                <span>Рейтинг</span>
                                                <input type="radio" id="radio-4" checked>
                                                <label for="radio-4"></label>
                                            </span>
                                        <?php else: ?>
                                            <a class="list__link" href="#" data-sort="rating" data-type="desc">
                                                <span>Рейтинг</span>
                                                <input type="radio" id="radio-4">
                                                <label for="radio-4"></label>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <!-- section-->

            <? ob_start(); ?>
            <div class="modal modal_filters" id="filters-modal">
                <div class="modal__container">
                    <?
                    $filterData = $APPLICATION->IncludeComponent(
                        "naturalist:empty",
                        "catalog_filters",
                        array(
                            "arFilterValues" => $arResult['SECTION_FILTER_VALUES'],
                            "dateFrom" => $arResult['arUriParams']['dateFrom'],
                            "dateTo" => $arResult['arUriParams']['dateTo'],
                            "arDates" => $arDates,
                            "currMonthName" => $currMonthName,
                            "currYear" => $currYear,
                            "nextYear" => $nextYear,
                            "guests" => $arResult['arUriParams']['guests'],
                            "children" => $arResult['arUriParams']['children'],
                            "guestsDeclension" => $arResult['guestsDeclension'],
                            "arChildrenAge" => $arResult['arUriParams']['childrenAge'],
                            "arHLTypes" => $arResult['arHLTypes'],
                            "arFilterTypes" => $arResult['arFilterTypes'],
                            "arServices" => $arResult['arServices'],
                            "arHLFood" => $arResult['arHLFood'],
                            "arFilterFood" => $arResult['arFilterFood'],
                            "arHLFeatures" => $arResult['arHLFeatures'],
                            "arFilterFeatures" => $arResult['arFilterFeatures'],
                            "arFilterServices" => $arResult['arFilterServices'],
                            "houseTypes" => $arResult['houseTypeData'],
                            "arFilterHouseTypes" => $arResult['arFilterHousetypes'],
                            "restVariants" => $arResult['restVariants'],
                            "arFilterRestVariants" => $arResult['arFilterRestVariants'],
                            "objectComforts" => $arResult['objectComforts'],
                            "arFilterObjectComforts" => $arResult['arFilterObjectComforts'],
                            "water" => $arResult['water'],
                            "arFilterWater" => $arResult['arFilterWater'],
                            "commonWater" => $arResult['commonWater'],
                            "difFilter" => $arResult['difFilter'],
                            "arFilterCommonWater" => $arResult['arFilterCommonWater'],
                            "arDifFilters" => $arResult['arDifFilters'],
                            "maxPrice" => $arResult['maxPrice'],
                            "minPrice" => $arResult['minPrice'],
                        )
                    );
                    ?>
                </div>
            </div>
            <?
            $content = ob_get_contents();
            ob_end_clean();
            ?>
            <? $APPLICATION->AddViewContent("filters-modal", $content); ?>

            <section class="section_catalog">
                <div class="container">
                    <?
                    $APPLICATION->IncludeComponent(
                        "naturalist:empty",
                        "catalog",
                        array(
                            "sortBy" => $arResult['arSort'],
                            "orderReverse" => $arResult['orderReverse'],
                            "page" => $arParams["REAL_PAGE"] ? $arParams["REAL_PAGE"] : $page,
                            "pageCount" => $pageCount,
                            "allCount" => count($arResult['SECTIONS']),
                            "countDeclension" => $arResult['countDeclension'],
                            "reviewsDeclension" => $arResult['reviewsDeclension'],
                            "arPageSections" => $arPageSections,
                            "arReviewsAvg" => $arResult['arReviewsAvg'],
                            "arFavourites" => $arResult['FAVORITES'],
                            "arHLTypes" => $arResult['arHLTypes'],
                            "arHLFeatures" => $arResult['arHLFeatures'],
                            "arServices" => $arResult['arServices'],
                            "arSearchedRegions" => is_array($arResult['arRegionIds']) ? array_unique($arResult['arRegionIds']) : '',
                            "searchedRegionData" => $searchedRegionData,
                            "searchName" => $searchName ?? $search,
                            "arFilterValues" => $arResult['SECTION_FILTER_VALUES'],
                            "dateFrom" => $arResult['arUriParams']['dateFrom'],
                            "dateTo" => $arResult['arUriParams']['dateTo'],
                            "arDates" => $arDates,
                            "currMonthName" => $currMonthName,
                            "currYear" => $currYear,
                            "nextYear" => $nextYear,
                            "guests" => $arResult['arUriParams']['guests'],
                            "children" => $arResult['arUriParams']['children'],
                            "guestsDeclension" => $arResult['guestsDeclension'],
                            "arChildrenAge" => $arResult['arUriParams']['childrenAge'],
                            "itemsCount" => $arParams["ITEMS_COUNT"],
                            'filterData' => $filterData,
                            "arFilterTypes" => $arResult['arFilterTypes'],
                        )
                    );
                    ?>
                </div>
            </section>
            <!-- section-->
            <?php if (CSite::InDir('/map') == false): ?>
                <section class="cert-index__seo-text">
                    <div class="container">
                        <? if (!empty($arSeoImpressions) && reset($arSeoImpressions)['PREVIEW_TEXT'] != '') {
                            echo reset($arSeoImpressions)['PREVIEW_TEXT'];
                            $isSeoText = true;
                        } else if (empty($_GET)) {
                            $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => '/include/' . $arResult['SEO_FILE'] . '-seo-text.php',
                                    "EDIT_TEMPLATE" => ""
                                )
                            );
                            $isSeoText = true;
                        } else if ($arResult['CHPY_SEO_TEXT']) {
                            echo $arResult['CHPY_SEO_TEXT'];
                            $isSeoText = true;
                        } else if (isset($arResult['pageSeoData']) && isset($arResult['pageSeoData']['UF_SEO_TEXT'])) {
                            echo $arResult['pageSeoData']['UF_SEO_TEXT'];
                            $isSeoText = true;
                        } ?>
                    </div>
                </section>
                <? if ($isSeoText) { ?>
                    <div class="container">
                        <a href="#" class="show-more-seo">Показать ещё</a>
                    </div>
                <? } ?>
            <? endif; ?>
        </main>
        <div class="mobile-link__btn">
            <?php if (CSite::InDir('/map')): ?>
                <a href="/catalog/">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_4746_17082)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.89876 6.9925C2.09804 6.94873 2.32065 6.9375 2.55 6.9375H15.45C15.6793 6.9375 15.902 6.94873 16.1012 6.9925C16.3069 7.03768 16.5219 7.12484 16.6985 7.30149C16.8752 7.47814 16.9623 7.69312 17.0075 7.89876C17.0513 8.09804 17.0625 8.32065 17.0625 8.55V9.45C17.0625 9.67935 17.0513 9.90196 17.0075 10.1012C16.9623 10.3069 16.8752 10.5219 16.6985 10.6985C16.5219 10.8752 16.3069 10.9623 16.1012 11.0075C15.902 11.0513 15.6793 11.0625 15.45 11.0625H2.55C2.32065 11.0625 2.09804 11.0513 1.89876 11.0075C1.69312 10.9623 1.47814 10.8752 1.30149 10.6985C1.12484 10.5219 1.03768 10.3069 0.992502 10.1012C0.948725 9.90196 0.9375 9.67935 0.9375 9.45V8.55C0.9375 8.32065 0.948725 8.09804 0.992502 7.89876C1.03768 7.69312 1.12484 7.47814 1.30149 7.30149C1.47814 7.12484 1.69312 7.03768 1.89876 6.9925ZM2.0913 8.14015C2.07394 8.21919 2.0625 8.345 2.0625 8.55V9.45C2.0625 9.655 2.07394 9.78081 2.0913 9.85985C2.09502 9.87678 2.09855 9.8894 2.10149 9.89851C2.1106 9.90145 2.12322 9.90498 2.14015 9.9087C2.21919 9.92606 2.345 9.9375 2.55 9.9375H15.45C15.655 9.9375 15.7808 9.92606 15.8599 9.9087C15.8768 9.90498 15.8894 9.90145 15.8985 9.89851C15.9015 9.8894 15.905 9.87678 15.9087 9.85985C15.9261 9.78081 15.9375 9.655 15.9375 9.45V8.55C15.9375 8.345 15.9261 8.21919 15.9087 8.14015C15.905 8.12322 15.9015 8.1106 15.8985 8.10149C15.8894 8.09855 15.8768 8.09502 15.8599 8.0913C15.7808 8.07394 15.655 8.0625 15.45 8.0625H2.55C2.345 8.0625 2.21919 8.07394 2.14015 8.0913C2.12322 8.09502 2.1106 8.09855 2.10149 8.10149C2.09855 8.1106 2.09502 8.12322 2.0913 8.14015ZM2.10941 8.08143C2.10942 8.08145 2.10918 8.08192 2.10866 8.08278C2.10914 8.08183 2.1094 8.0814 2.10941 8.08143ZM2.08277 8.10866C2.08192 8.10918 2.08145 8.10942 2.08143 8.10941C2.0814 8.1094 2.08183 8.10914 2.08277 8.10866ZM15.9186 8.10941C15.9186 8.10942 15.9181 8.10918 15.9173 8.10868C15.9182 8.10914 15.9186 8.1094 15.9186 8.10941ZM15.8914 8.08282C15.8908 8.08194 15.8906 8.08145 15.8906 8.08143C15.8906 8.0814 15.8909 8.08184 15.8914 8.08282ZM15.8906 9.91857C15.8906 9.91855 15.8908 9.91809 15.8913 9.91725C15.8909 9.91818 15.8906 9.9186 15.8906 9.91857ZM15.9172 9.89136C15.9181 9.89082 15.9186 9.89058 15.9186 9.89059C15.9186 9.8906 15.9182 9.89088 15.9172 9.89136ZM2.08143 9.89059C2.08145 9.89058 2.08192 9.89082 2.08278 9.89134C2.08183 9.89086 2.0814 9.8906 2.08143 9.89059ZM2.10867 9.91723C2.10919 9.91808 2.10942 9.91855 2.10941 9.91857C2.1094 9.9186 2.10914 9.91817 2.10867 9.91723Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.89876 0.992502C2.09804 0.948725 2.32065 0.9375 2.55 0.9375H15.45C15.6793 0.9375 15.902 0.948725 16.1012 0.992502C16.3069 1.03768 16.5219 1.12484 16.6985 1.30149C16.8752 1.47814 16.9623 1.69312 17.0075 1.89876C17.0513 2.09804 17.0625 2.32065 17.0625 2.55V3.45C17.0625 3.67935 17.0513 3.90196 17.0075 4.10124C16.9623 4.30688 16.8752 4.52186 16.6985 4.69851C16.5219 4.87516 16.3069 4.96232 16.1012 5.0075C15.902 5.05127 15.6793 5.0625 15.45 5.0625H2.55C2.32065 5.0625 2.09804 5.05127 1.89876 5.0075C1.69312 4.96232 1.47814 4.87516 1.30149 4.69851C1.12484 4.52186 1.03768 4.30688 0.992502 4.10124C0.948725 3.90196 0.9375 3.67935 0.9375 3.45V2.55C0.9375 2.32065 0.948725 2.09804 0.992502 1.89876C1.03768 1.69312 1.12484 1.47814 1.30149 1.30149C1.47814 1.12484 1.69312 1.03768 1.89876 0.992502ZM2.0913 2.14015C2.07394 2.21919 2.0625 2.345 2.0625 2.55V3.45C2.0625 3.655 2.07394 3.78081 2.0913 3.85985C2.09502 3.87678 2.09855 3.8894 2.10149 3.89851C2.1106 3.90145 2.12322 3.90498 2.14015 3.9087C2.21919 3.92606 2.345 3.9375 2.55 3.9375H15.45C15.655 3.9375 15.7808 3.92606 15.8599 3.9087C15.8768 3.90498 15.8894 3.90145 15.8985 3.89851C15.9015 3.8894 15.905 3.87678 15.9087 3.85985C15.9261 3.78081 15.9375 3.655 15.9375 3.45V2.55C15.9375 2.345 15.9261 2.21919 15.9087 2.14015C15.905 2.12322 15.9015 2.1106 15.8985 2.10149C15.8894 2.09855 15.8768 2.09502 15.8599 2.0913C15.7808 2.07394 15.655 2.0625 15.45 2.0625H2.55C2.345 2.0625 2.21919 2.07394 2.14015 2.0913C2.12322 2.09502 2.1106 2.09855 2.10149 2.10149C2.09855 2.1106 2.09502 2.12322 2.0913 2.14015ZM2.10941 2.08143C2.10942 2.08145 2.10918 2.08192 2.10866 2.08278C2.10914 2.08183 2.1094 2.0814 2.10941 2.08143ZM2.08277 2.10866C2.08192 2.10918 2.08145 2.10942 2.08143 2.10941C2.0814 2.1094 2.08183 2.10914 2.08277 2.10866ZM15.9186 2.10941C15.9186 2.10942 15.9181 2.10918 15.9173 2.10868C15.9182 2.10914 15.9186 2.1094 15.9186 2.10941ZM15.8914 2.08282C15.8908 2.08194 15.8906 2.08145 15.8906 2.08143C15.8906 2.0814 15.8909 2.08184 15.8914 2.08282ZM15.8906 3.91857C15.8906 3.91855 15.8908 3.91809 15.8913 3.91725C15.8909 3.91818 15.8906 3.9186 15.8906 3.91857ZM15.9172 3.89136C15.9181 3.89082 15.9186 3.89058 15.9186 3.89059C15.9186 3.8906 15.9182 3.89088 15.9172 3.89136ZM2.08143 3.89059C2.08145 3.89058 2.08192 3.89082 2.08278 3.89134C2.08183 3.89086 2.0814 3.8906 2.08143 3.89059ZM2.10867 3.91723C2.10919 3.91808 2.10942 3.91855 2.10941 3.91857C2.1094 3.9186 2.10914 3.91817 2.10867 3.91723Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.89876 12.9925C2.09804 12.9487 2.32065 12.9375 2.55 12.9375H15.45C15.6793 12.9375 15.902 12.9487 16.1012 12.9925C16.3069 13.0377 16.5219 13.1248 16.6985 13.3015C16.8752 13.4781 16.9623 13.6931 17.0075 13.8988C17.0513 14.098 17.0625 14.3207 17.0625 14.55V15.45C17.0625 15.6793 17.0513 15.902 17.0075 16.1012C16.9623 16.3069 16.8752 16.5219 16.6985 16.6985C16.5219 16.8752 16.3069 16.9623 16.1012 17.0075C15.902 17.0513 15.6793 17.0625 15.45 17.0625H2.55C2.32065 17.0625 2.09804 17.0513 1.89876 17.0075C1.69312 16.9623 1.47814 16.8752 1.30149 16.6985C1.12484 16.5219 1.03768 16.3069 0.992502 16.1012C0.948725 15.902 0.9375 15.6793 0.9375 15.45V14.55C0.9375 14.3207 0.948725 14.098 0.992502 13.8988C1.03768 13.6931 1.12484 13.4781 1.30149 13.3015C1.47814 13.1248 1.69312 13.0377 1.89876 12.9925ZM2.0913 14.1401C2.07394 14.2192 2.0625 14.345 2.0625 14.55V15.45C2.0625 15.655 2.07394 15.7808 2.0913 15.8599C2.09502 15.8768 2.09855 15.8894 2.10149 15.8985C2.1106 15.9015 2.12322 15.905 2.14015 15.9087C2.21919 15.9261 2.345 15.9375 2.55 15.9375H15.45C15.655 15.9375 15.7808 15.9261 15.8599 15.9087C15.8768 15.905 15.8894 15.9015 15.8985 15.8985C15.9015 15.8894 15.905 15.8768 15.9087 15.8599C15.9261 15.7808 15.9375 15.655 15.9375 15.45V14.55C15.9375 14.345 15.9261 14.2192 15.9087 14.1401C15.905 14.1232 15.9015 14.1106 15.8985 14.1015C15.8894 14.0985 15.8768 14.095 15.8599 14.0913C15.7808 14.0739 15.655 14.0625 15.45 14.0625H2.55C2.345 14.0625 2.21919 14.0739 2.14015 14.0913C2.12322 14.095 2.1106 14.0985 2.10149 14.1015C2.09855 14.1106 2.09502 14.1232 2.0913 14.1401ZM2.10941 14.0814C2.10942 14.0815 2.10918 14.0819 2.10866 14.0828C2.10914 14.0818 2.1094 14.0814 2.10941 14.0814ZM2.08277 14.1087C2.08192 14.1092 2.08145 14.1094 2.08143 14.1094C2.0814 14.1094 2.08183 14.1091 2.08277 14.1087ZM15.9186 14.1094C15.9186 14.1094 15.9181 14.1092 15.9173 14.1087C15.9182 14.1091 15.9186 14.1094 15.9186 14.1094ZM15.8914 14.0828C15.8908 14.0819 15.8906 14.0814 15.8906 14.0814C15.8906 14.0814 15.8909 14.0818 15.8914 14.0828ZM15.8906 15.9186C15.8906 15.9186 15.8908 15.9181 15.8913 15.9173C15.8909 15.9182 15.8906 15.9186 15.8906 15.9186ZM15.9172 15.8914C15.9181 15.8908 15.9186 15.8906 15.9186 15.8906C15.9186 15.8906 15.9182 15.8909 15.9172 15.8914ZM2.08143 15.8906C2.08145 15.8906 2.08192 15.8908 2.08278 15.8913C2.08183 15.8909 2.0814 15.8906 2.08143 15.8906ZM2.10867 15.9172C2.10919 15.9181 2.10942 15.9186 2.10941 15.9186C2.1094 15.9186 2.10914 15.9182 2.10867 15.9172Z" fill="white" />
                        </g>
                        <defs>
                            <clipPath id="clip0_4746_17082">
                                <rect width="18" height="18" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                    <span>Каталог</span>
                </a>
            <? else: ?>
                <a href="/map/">
                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.85693 2.49565L11.3791 3.74973C11.7107 3.91461 11.8007 3.95523 11.8877 3.97572C11.9745 3.99614 12.0722 3.99987 12.441 3.99987L14.0397 3.99987C14.713 3.99985 15.2756 3.99983 15.7225 4.06124C16.1956 4.12625 16.6217 4.26942 16.9627 4.61803C17.3021 4.96492 17.4399 5.39534 17.5028 5.873C17.5625 6.3273 17.5625 6.9002 17.5625 7.59017V12.7222C17.5625 13.4122 17.5625 13.9851 17.5028 14.4394C17.4399 14.917 17.3021 15.3474 16.9627 15.6943C16.6217 16.0429 16.1956 16.1861 15.7225 16.2511C15.2756 16.3125 14.713 16.3125 14.0397 16.3125H12.441C12.4254 16.3125 12.4099 16.3125 12.3947 16.3125C12.0974 16.3127 11.8608 16.3128 11.6299 16.2584C11.3993 16.2041 11.1871 16.0985 10.9199 15.9655C10.9061 15.9586 10.8922 15.9517 10.8782 15.9447L8.01776 14.5224C7.29633 14.1637 7.04916 14.0486 6.80254 14.0149C6.24352 13.9386 5.78734 14.2032 5.08867 14.6086L5.07216 14.6181L5.04559 14.6335C4.58132 14.9029 4.19556 15.1267 3.87612 15.2709C3.55302 15.4168 3.20251 15.5277 2.83268 15.4617C2.43266 15.3903 2.07352 15.1754 1.81875 14.8594C1.58507 14.5694 1.50745 14.2107 1.47229 13.8536C1.43748 13.5 1.43749 13.0479 1.4375 12.501L1.4375 6.32178C1.43749 5.9084 1.43748 5.55863 1.4656 5.26884C1.49546 4.96111 1.56005 4.67714 1.71498 4.40518C1.87029 4.13257 2.08136 3.93344 2.33063 3.75344C2.56435 3.58467 2.86296 3.41146 3.21402 3.20782C3.22163 3.20341 3.22927 3.19898 3.23693 3.19453L4.18043 2.64721C4.66573 2.36568 5.06319 2.1351 5.40882 1.97548C5.77027 1.80854 6.11179 1.70098 6.49105 1.68867C6.87035 1.67637 7.21799 1.7616 7.58924 1.90484C7.94415 2.04178 8.35518 2.24616 8.85693 2.49565ZM7.18427 2.95443C6.89496 2.8428 6.70392 2.80736 6.52752 2.81308C6.35107 2.81881 6.16249 2.86658 5.88052 2.99681C5.58807 3.13188 5.23575 3.33561 4.72234 3.63344L3.80144 4.16765C3.42128 4.38818 3.17195 4.53357 2.98924 4.66551C2.81699 4.78988 2.74092 4.87705 2.69249 4.96206C2.64369 5.04772 2.60641 5.1603 2.58534 5.37749C2.56311 5.60656 2.5625 5.90147 2.5625 6.34776V12.4712C2.5625 13.0554 2.56315 13.4516 2.59188 13.7434C2.6209 14.0382 2.67163 14.1248 2.69465 14.1534C2.7815 14.2611 2.90108 14.3311 3.03032 14.3542C3.05819 14.3592 3.14998 14.3644 3.41321 14.2455C3.67403 14.1278 4.01001 13.9337 4.50765 13.645C4.54722 13.6221 4.58762 13.5984 4.62884 13.5743C5.22783 13.2234 6.00163 12.7701 6.95477 12.9003C7.40773 12.9621 7.82974 13.1722 8.43672 13.4743C8.46366 13.4877 8.49096 13.5013 8.51864 13.5151L11.3791 14.9374C11.7107 15.1022 11.8007 15.1429 11.8877 15.1633C11.9745 15.1838 12.0722 15.1875 12.441 15.1875H14C14.7234 15.1875 15.208 15.1863 15.5693 15.1366C15.9132 15.0894 16.0603 15.008 16.1586 14.9076C16.2585 14.8054 16.3404 14.65 16.3874 14.2926C16.4364 13.9204 16.4375 13.422 16.4375 12.6838V7.62861C16.4375 6.89032 16.4364 6.39199 16.3874 6.01972C16.3404 5.6624 16.2585 5.50694 16.1586 5.40479C16.0603 5.30436 15.9132 5.22301 15.5693 5.17576C15.208 5.12612 14.7234 5.12487 14 5.12487H12.441C12.4254 5.12487 12.4099 5.12488 12.3946 5.12489C12.0974 5.12503 11.8608 5.12514 11.6299 5.07078C11.3993 5.01649 11.1871 4.91084 10.9199 4.77782C10.9061 4.77098 10.8922 4.76406 10.8782 4.75707L8.37947 3.51465C7.84859 3.25068 7.48442 3.07023 7.18427 2.95443Z" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.5 1.6875C6.81066 1.6875 7.0625 1.93934 7.0625 2.25L7.0625 13.125C7.0625 13.4357 6.81066 13.6875 6.5 13.6875C6.18934 13.6875 5.9375 13.4357 5.9375 13.125L5.9375 2.25C5.9375 1.93934 6.18934 1.6875 6.5 1.6875Z" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.75 4.3125C12.0607 4.3125 12.3125 4.56434 12.3125 4.875L12.3125 15.375C12.3125 15.6857 12.0607 15.9375 11.75 15.9375C11.4393 15.9375 11.1875 15.6857 11.1875 15.375L11.1875 4.875C11.1875 4.56434 11.4393 4.3125 11.75 4.3125Z" fill="white" />
                    </svg>
                    <span>На карте</span>
                </a>
            <? endif; ?>
        </div>
        <? $APPLICATION->ShowViewContent('filters-modal'); ?>

        <?
        $APPLICATION->IncludeComponent(
            "naturalist:empty",
            "catalog_scripts",
            array(
                "arSections" => $arResult['SECTIONS'],
                "arFavourites" => $arResult['FAVORITES'],
                "arReviewsAvg" => $arResult['arReviewsAvg'],
                "map" => $arParams["MAP"]
            )
        );
