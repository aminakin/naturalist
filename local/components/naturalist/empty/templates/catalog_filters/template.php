<?
foreach ($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="filters__heading">
    <div class="h3">Фильтры</div>
    <button class="modal__close" data-modal-close>
        <span>Скрыть</span>
        <svg class="icon icon_cross" viewbox="0 0 18 18">
            <use xlink:href="#cross" />
        </svg>
    </button>
</div>
<form class="form filters <?php if (CSite::InDir('/map')): ?>filter__on_map<?php endif; ?>" id="form-catalog-filter">
    <div class="filters__form" id="popup_filter_body">
        <div class="form__group mainsearch">
            <div class="form_group_wrapper">
                <div class="form__item name">
                    <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                        <input type="hidden" data-autocomplete-result
                            value='<?= ($arFilterValues["SEARCH"]) ? $arFilterValues["SEARCH"] : null ?>'>
                        <input class="field__input" type="text" name="name" placeholder="Укажите место или глэмпинг"
                            data-autocomplete-field
                            value='<?= ($arFilterValues["SEARCH_TEXT"]) ? $arFilterValues["SEARCH_TEXT"] : null ?>'>
                        <div class="autocomplete-dropdown" data-autocomplete-dropdown></div>
                    </div>
                </div>

                <div class="form_group_wrapper-filter_items">
                    <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today"
                        data-calendar-max="365">
                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <div class="field__input" data-calendar-label="data-calendar-label"
                                    data-date-from><? if ($dateFrom): ?><?= $dateFrom ?><? else: ?>
                                    <span>Заезд</span><? endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form__item">
                            <div class="field field_icon field_calendar">
                                <div class="field__input" data-calendar-label="data-calendar-label"
                                    data-date-to><? if ($dateTo): ?><?= $dateTo ?><? else: ?>
                                    <span>Выезд</span><? endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="calendar__dropdown" data-calendar-dropdown="data-calendar-dropdown">
                            <div class="calendar__navigation">
                                <div class="calendar__navigation-item calendar__navigation-item_months">
                                    <div class="calendar__navigation-label"
                                        data-calendar-navigation="data-calendar-navigation">
                                        <span><?= $currMonthName ?></span>
                                    </div>
                                    <ul class="list">
                                        <?
                                        $k = 0;
                                        ?>
                                        <? foreach ($arDates[0] as $monthName) : ?>
                                            <li class="list__item<? if ($k == 0) : ?> list__item_active<? endif; ?>">
                                                <button data-calendar-year="<?= $currYear ?>" class="list__item-month"
                                                    data-calendar-month-select="<?= $k ?>"
                                                    type="button"><?= $monthName ?></button>
                                            </li>
                                            <? $k++; ?>
                                        <? endforeach ?>
                                        <li class="list__item" data-calendar-delimiter="data-calendar-delimiter">
                                            <div class="list__item-year"><?= $nextYear ?></div>
                                        </li>
                                        <? foreach ($arDates[1] as $monthName) : ?>
                                            <li class="list__item">
                                                <button data-calendar-year="<?= $nextYear ?>" class="list__item-month"
                                                    data-calendar-month-select="<?= $k ?>"
                                                    type="button"><?= $monthName ?></button>
                                            </li>
                                            <? $k++; ?>
                                        <? endforeach ?>
                                    </ul>
                                </div>

                                <div class="calendar__navigation-item calendar__navigation-item_years">
                                    <div class="calendar__navigation-label"
                                        data-calendar-navigation="data-calendar-navigation">
                                        <span><?= $currYear ?></span>
                                    </div>
                                    <ul class="list">
                                        <li class="list__item list__item_active">
                                            <button data-calendar-year-select="<?= $currYear ?>"
                                                type="button"><?= $currYear ?></button>
                                        </li>
                                        <li class="list__item">
                                            <button data-calendar-year-select="<?= $nextYear ?>"
                                                type="button"><?= $nextYear ?></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="calendar__month">
                                <input type="hidden" data-calendar-value="data-calendar-value">
                            </div>
                        </div>
                    </div>

                    <div class="form__item guest">
                        <div class="field field_icon guests" data-guests="data-guests">
                            <div class="field__input"
                                data-guests-control="data-guests-control"><?= $guests + $children ?> <?= $guestsDeclension->get($guests + $children) ?></div>

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
                                                name="guests-adults-count" value="<?= $guests ?>" data-min="1">
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
                                                name="guests-children-count" value="<?= $children ?>" data-min="0">
                                            <button class="counter__plus" type="button"></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="guests__children" data-guests-children="data-guests-children">
                                    <? if ($arChildrenAge): ?>
                                        <? foreach ($arChildrenAge as $keyAge => $valueAge): ?>
                                            <div class="guests__item">
                                                <div class="guests__label">
                                                    <div><?= GetMessage('FILTER_CHILD_AGE') ?></div>
                                                    <span><?= getChildrenOrderTitle($keyAge + 1) ?> <?= GetMessage('FILTER_CHILD') ?></span>
                                                </div>
                                                <div class="counter">
                                                    <button class="counter__minus" type="button">
                                                        <svg class="icon icon_arrow-small" viewBox="0 0 16 16"
                                                            style="width: 1.6rem; height: 1.6rem;">
                                                            <use xlink:href="#arrow-small"></use>
                                                        </svg>
                                                    </button>
                                                    <input type="text" disabled="" data-guests-children=""
                                                        name="guests-children-<?= $keyAge ?>"
                                                        value="<?= $valueAge ?>"
                                                        data-min="0" data-max="17">
                                                    <button class="counter__plus" type="button">
                                                        <svg class="icon icon_arrow-small" viewBox="0 0 16 16"
                                                            style="width: 1.6rem; height: 1.6rem;">
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
                        <button class="button button_primary" data-filter-set>Найти</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filters_attrs">
            <div class="filters-attrs__block">
                <div class="form__dropdown form__dropdown_show">
                    <div class="price-filter">
                        <div class="form__dropdown-heading h6">Цена</div>
                        <div class="price-inputs__wrap">
                            <div class="price-input__wrap">
                                <label for="min-price">от</label>
                                <input type="text" class="min-price" data-price-value="<?= $minPrice ?>" name="min-price" value="<?= $minPrice ?>" size="5">
                                <span>&nbsp;₽</span>
                            </div>
                            <div class="price-input__wrap">
                                <label for="max-price">до</label>
                                <input type="text" class="max-price" data-price-value="<?= $maxPrice ?>" name="max-price" value="<?= $maxPrice ?>" size="5">
                                <span>&nbsp;₽</span>
                            </div>
                        </div>
                        <div class="slider-range"></div>
                    </div>
                </div>
                <div class="form__dropdown form__dropdown_show">
                    <div class="form__dropdown-heading h6">Тип размещения</div>
                    <div class="form__dropdown-body">
                        <ul class="list list_checkboxes">
                            <? foreach ($arHLTypes as $arType) : ?>
                                <? $onclick = ""; ?>
                                <? if ($arType["UF_NAME"] === "Кемпинг"): ?>
                                    <? $onclick = "onclick=\"ym(91071014, 'reachGoal', 'camping'); return true;\""; ?>
                                <? elseif ($arType["UF_NAME"] === "Глэмпинг"): ?>
                                    <? $onclick = "onclick=\"ym(91071014, 'reachGoal', 'glamping'); return true;\""; ?>
                                <? elseif ($arType["UF_NAME"] === "Отель"): ?>
                                    <? $onclick = "onclick=\"ym(91071014, 'reachGoal', 'hotel'); return true;\""; ?>
                                <? endif; ?>
                                <li class="list__item">
                                    <label class="checkbox">
                                        <input type="checkbox" name="type" <?= $onclick; ?> value="<?= $arType["ID"] ?>"
                                            <? if ($arFilterTypes && in_array($arType["ID"], $arFilterTypes)) : ?>checked<? endif; ?>><span><?= $arType["UF_NAME"] ?></span>
                                    </label>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </div>
                </div>

                <? if ($restVariants): ?>
                    <div class="form__dropdown form__dropdown_show">
                        <div class="form__dropdown-heading h6">Варианты отдыха</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($restVariants as $restVariant) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="restvariants" value="<?= $restVariant["ID"] ?>"
                                                <? if ($arFilterRestVariants && in_array($restVariant["ID"], $arFilterRestVariants)) : ?>checked<? endif; ?>><span><?= $restVariant["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? endif; ?>

                <? if ($water): ?>
                    <div class="form__dropdown form__dropdown_show" style="display: none">
                        <div class="form__dropdown-heading h6">Водоёмы</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($water as $oneWater) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="water" value="<?= $oneWater["ID"] ?>"
                                                <? if ($arFilterWater && in_array($oneWater["ID"], $arFilterWater)) : ?>checked<? endif; ?>><span><?= $oneWater["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? endif; ?>

                <? if ($commonWater): ?>
                    <div class="form__dropdown form__dropdown_show" style="display: none">
                        <div class="form__dropdown-heading h6">Общие водоёмы</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($commonWater as $oneCommonWater) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="commonwater" value="<?= $oneCommonWater["ID"] ?>"
                                                <? if ($arFilterCommonWater && in_array($oneCommonWater["ID"], $arFilterCommonWater)) : ?>checked<? endif; ?>><span><?= $oneCommonWater["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? endif; ?>
            </div>

            <div class="filters-attrs__block">
                <? if ($houseTypes) { ?>
                    <div class="form__dropdown form__dropdown_show">
                        <div class="form__dropdown-heading h6">Типы домов</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($houseTypes as $houseType) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="housetypes" value="<?= $houseType["ID"] ?>"
                                                <? if ($arFilterHouseTypes && in_array($houseType["ID"], $arFilterHouseTypes)) : ?>checked<? endif; ?>><span><?= $houseType["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? } ?>

                <? if ($arServices): ?>
                    <div class="form__dropdown form__dropdown_show">
                        <div class="form__dropdown-heading h6">Окружение</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($arServices as $arService) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="services" value="<?= $arService["ID"] ?>"
                                                <? if ($arFilterServices && in_array($arService["ID"], $arFilterServices)) : ?>checked<? endif; ?>><span><?= $arService["NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? endif; ?>
            </div>

            <div class="filters-attrs__block">
                <? if ($arHLFood): ?>
                    <div class="form__dropdown form__dropdown_show">
                        <div class="form__dropdown-heading h6">Питание</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($arHLFood as $arFoodItem) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="food" value="<?= $arFoodItem["ID"] ?>"
                                                <? if ($arFilterFood && in_array($arFoodItem["ID"], $arFilterFood)) : ?>checked<? endif; ?>><span><?= $arFoodItem["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? endif; ?>

                <? if ($objectComforts) { ?>
                    <div class="form__dropdown form__dropdown_show">
                        <div class="form__dropdown-heading h6">Удобства</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($objectComforts as $objectComfort) : ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="objectcomforts" value="<?= $objectComfort["ID"] ?>"
                                                <? if ($arFilterObjectComforts && in_array($objectComfort["ID"], $arFilterObjectComforts)) : ?>checked<? endif; ?>><span><?= $objectComfort["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? } ?>
            </div>

            <div class="filters-attrs__block">
                <? if ($arHLFeatures): ?>
                    <div class="form__dropdown form__dropdown_show">
                        <div class="form__dropdown-heading h6">Впечатления</div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                                <? foreach ($arHLFeatures as $arFeature) : ?>
                                    <?
                                    if ($arFeature["UF_SHOW_FILTER"] != 1) {
                                        continue;
                                    }
                                    ?>
                                    <li class="list__item">
                                        <label class="checkbox">
                                            <input type="checkbox" name="features" value="<?= $arFeature["ID"] ?>"
                                                <? if ($arFilterFeatures && in_array(
                                                    $arFeature["ID"],
                                                    $arFilterFeatures
                                                )) : ?>checked<? endif; ?>><span><?= $arFeature["UF_NAME"] ?></span>
                                        </label>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>

    <div class="filters__controls">
        <button class="button button-clear" data-filter-reset>Сбросить</button>
        <button class="button button_primary button-accept" data-filter-set>Применить</button>
    </div>
</form>