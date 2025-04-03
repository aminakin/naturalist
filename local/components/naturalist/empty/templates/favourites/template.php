<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;

foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>

<main class="main">
    <section class="section section_crumbs">
        <div class="container">
            <div class="crumbs">
                <ul class="list crumbs__list">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:breadcrumb",
                        "main",
                        array(
                            "PATH" => "",
                            "SITE_ID" => "s1",
                            "START_FROM" => "0",
                            "COMPONENT_TEMPLATE" => "main"
                        ),
                        false
                    );
                    ?>
                </ul>
            </div>
        </div>
    </section>
    <!-- section-->

    <section class="section section_favorite">
        <div class="container">
            <div class="profile">
                <? if ($isAuthorized) : ?>
                    <div class="profile__sidebar">
                        <div class="profile-preview">
                            <?if($arUser["PERSONAL_PHOTO"]):?>
                                <div class="profile-preview__image">
                                    <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>" alt="<?= $arUser["NAME"] ?>" title="Фото - <?= $arUser["NAME"] ?>">
                                </div>
                            <?endif;?>
                            <div class="profile-preview__name"><?= $arUser["NAME"] ?></div>
                        </div>

                        <div class="sidebar-navigation">
                            <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span>Избранное</span></div>
                            <ul class="list">
                                <?
                                $APPLICATION->IncludeComponent(
                                    "bitrix:menu",
                                    "footer",
                                    array(
                                        "ROOT_MENU_TYPE" => "personal",
                                        "MAX_LEVEL" => "1",
                                        "CHILD_MENU_TYPE" => "",
                                        "USE_EXT" => "N",
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
                        </div>
                        <a class="button button_transparent" href="#feedback" data-modal>Связаться с нами</a>
                    </div>
                <?endif;?>

                <div class="profile__article">
                    <div class="profile__heading">
                        <h1>Избранное</h1>

                        <? if ($arFavourites) : ?>
                            <div class="profile__heading-controls">
                                <form class="form form_filters" id="form-favourites-search">
                                    <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today" data-calendar-max="365">
                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-from><?if($dateFrom):?><?=$dateFrom?><?else:?><span>Заезд</span><?endif;?></div>
                                            </div>
                                        </div>

                                        <div class="form__item">
                                            <div class="field field_icon field_calendar">
                                                <div class="field__input" data-calendar-label="data-calendar-label" data-date-to><?if($dateTo):?><?=$dateTo?><?else:?><span>Выезд</span><?endif;?></div>
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
                                                                <button data-calendar-year="<?= $currYear ?>" class="list__item-month" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
                                                            </li>
                                                            <? $k++; ?>
                                                        <? endforeach ?>
                                                        <li class="list__item" data-calendar-delimiter="data-calendar-delimiter">
                                                            <div class="list__item-year"><?= $nextYear ?></div>
                                                        </li>
                                                        <? foreach ($arDates[1] as $monthName) : ?>
                                                            <li class="list__item">
                                                                <button data-calendar-year="<?= $nextYear ?>" class="list__item-month" data-calendar-month-select="<?= $k ?>" type="button"><?= $monthName ?></button>
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
                                                <div class="field__input" data-guests-control="data-guests-control"><?=$guests + $children?> <?= $guestsDeclension->get($guests + $children) ?></div>

                                                <div class="guests__dropdown">
                                                    <div class="guests__guests">
                                                        <div class="guests__item">
                                                            <div class="guests__label">
                                                                <div><?=GetMessage('FILTER_ADULTS')?></div><span><?=GetMessage('FILTER_ADULTS_AGE')?></span>
                                                            </div>
                                                            <div class="counter">
                                                                <button class="counter__minus" type="button"></button>
                                                                <input type="text" disabled="disabled" data-guests-adults-count="data-guests-adults-count" name="guests-adults-count" value="<?=$guests?>" data-min="1" data-max="10">
                                                                <button class="counter__plus" type="button"></button>
                                                            </div>
                                                        </div>

                                                        <div class="guests__item">
                                                            <div class="guests__label">
                                                                <div><?=GetMessage('FILTER_CHILDREN')?></div><span><?=GetMessage('FILTER_CHILDREN_AGE')?></span>
                                                            </div>
                                                            <div class="counter">
                                                                <button class="counter__minus" type="button"></button>
                                                                <input type="text" disabled="disabled" data-guests-children-count="data-guests-children-count" name="guests-children-count" value="<?=$children?>" data-min="0" data-max="10">
                                                                <button class="counter__plus" type="button"></button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="guests__children" data-guests-children="data-guests-children">
                                                        <? if ($arChildrenAge): ?>
                                                            <? foreach ($arChildrenAge as $keyAge => $valueAge): ?>
                                                                <div class="guests__item">
                                                                    <div class="guests__label">
                                                                        <div><?=GetMessage('FILTER_CHILD_AGE')?></div>
                                                                        <span><?=getChildrenOrderTitle($keyAge + 1)?> <?=GetMessage('FILTER_CHILD')?></span>
                                                                    </div>
                                                                    <div class="counter">
                                                                        <button class="counter__minus" type="button">
                                                                            <svg class="icon icon_arrow-small" viewBox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                                                                <use xlink:href="#arrow-small"></use>
                                                                            </svg>
                                                                        </button>
                                                                        <input type="text" disabled="" data-guests-children=""
                                                                               name="guests-children-<?= $keyAge ?>"
                                                                               value="<?= $valueAge ?>"
                                                                               data-min="0" data-max="17">
                                                                        <button class="counter__plus" type="button">
                                                                            <svg class="icon icon_arrow-small" viewBox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                                                                <use xlink:href="#arrow-small"></use>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            <? endforeach; ?>
                                                        <? endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form__item">
                                            <button class="button button_primary" data-favourites-search>Найти</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?endif;?>
                    </div>

                    <div class="profile__content">
                        <? if ($arFavourites) : ?>
                            <div class="objects__list">
                                <? foreach ($arFavourites as $arSection) : ?>
                                    <div class="object">
                                        <div class="object__images">
                                            <div class="swiper slider-gallery" data-slider-object="data-slider-object">
                                                <div class="swiper-wrapper">

                                                    <? $keyPhoto = 1; ?>
                                                    <? foreach ($arSection["PICTURES"] as $arPhoto) : ?>
                                                        <? if (count($arSection["PICTURES"]) > 1): ?>
                                                            <?
                                                            $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " рис." . $keyPhoto;;
                                                            $title = "Фото - " . $arSection["NAME"] . " рис." . $keyPhoto;
                                                            ?>
                                                        <? else: ?>
                                                            <?
                                                            $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"];
                                                            $title = "Фото - " . $arSection["NAME"];
                                                            ?>

                                                        <? endif; ?>
                                                        <div class="swiper-slide">
                                                            <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                                                 data-src="<?= $arPhoto["src"] ?>">
                                                        </div>
                                                        <? $keyPhoto++; ?>
                                                    <? endforeach; ?>

                                                </div>

                                                <? if ($arSection["PICTURES"] && count($arSection["PICTURES"]) > 1) : ?>
                                                    <div class="swiper-button-prev">
                                                        <svg class="icon icon_arrow-small" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                                            <use xlink:href="#arrow-small" />
                                                        </svg>
                                                    </div>
                                                    <div class="swiper-button-next">
                                                        <svg class="icon icon_arrow-small" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                                            <use xlink:href="#arrow-small" />
                                                        </svg>
                                                    </div>
                                                    <div class="swiper-pagination"></div>
                                                <? endif; ?>
                                            </div>

                                            <button class="favorite" data-favourite-remove data-id="<?= $arSection["ID"] ?>">
                                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg">
                                            </button>

                                            <? if (!empty($arSection["UF_ACTION"])) : ?>
                                                <div class="tag"><?= $arSection["UF_ACTION"] ?></div>
                                            <? endif; ?>
                                        </div>
                                        <div class="object__info">
                                            <div class="object__heading">
                                                <a class="object__title" href="<?=$arSection["URL"]?>"><?= $arSection["NAME"] ?></a>
                                                <div class="score">
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt="Рейтинг">
                                                    <span><?= $arSection["RATING"] ?></span>
                                                </div>
                                            </div>

                                            <div class="object__marker">
                                                <div class="area-info">
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt="Маркер">
                                                    <div>
                                                        <? if (isset($arHLTypes[$arSection["UF_TYPE"]])) : ?><span><?= $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] ?></span><? endif; ?>
                                                        <? if (!empty($arSection["UF_DISTANCE"])) : ?><span><?= $arSection["UF_DISTANCE"] ?></span><? endif; ?>
                                                        <? if (!empty($arSection["UF_ADDRESS"])) : ?><span><?= $arSection["UF_ADDRESS"] ?></span><? endif; ?>
                                                    </div>
                                                </div>

                                                <div class="object__marker-map">
                                                    <a href="<?=$arSection["URL"]?>#map">На карте</a>
                                                </div>
                                            </div>
                                            <div class="object__price">
                                                <?= number_format((float)$arSection["PRICE"], 0, '.', ' ') ?> ₽
                                            </div>
                                        </div>
                                        <a class="button button_transparent" href="<?=$arSection["URL"]?>"><?= Loc::getMessage('FILTER_CHOOSE')?></a>
                                    </div>
                                <? endforeach; ?>
                            </div>
                        <? else : ?>
                            <div class="profile__empty">
                                <div class="profile__empty-title">Нет избранного</div>
                                <div class="profile__empty-text">Здесь мы покажем отели, которые вы отметите как понравившиеся при <a href="/catalog/">следующем поиске</a>.</div>
                            </div>
                        <? endif; ?>

                        <? if (!$isAuthorized) : ?>
                            <div class="profile__login">
                                <div class="profile__login-item">
                                    <div>Для более удобной работы с избранным рекомендуем</div>
                                    <a class="button button_transparent" href="#login-phone" data-modal>Войти в личный кабинет</a>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->