<?
foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<form class="form filters" id="form-catalog-filter">
    <div class="filters__heading">
        <div class="h3">Фильтр</div>
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
    </div>

    <div class="filters__form">
        <div class="form__group">
            <div class="form__item">
                <div class="field field_autocomplete" data-autocomplete="/ajax/autocomplete.php">
                    <input type="hidden" data-autocomplete-result value='<?= ($arFilterValues["SEARCH"]) ? $arFilterValues["SEARCH"] : null?>'>
                    <input class="field__input" type="text" name="name" placeholder="Укажите место или глэмпинг" data-autocomplete-field value='<?= ($arFilterValues["SEARCH_TEXT"]) ? $arFilterValues["SEARCH_TEXT"] : null?>'>
                    <div class="autocomplete-dropdown" data-autocomplete-dropdown></div>
                </div>
            </div>

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

            <div class="form__item">
                <div class="field field_icon guests" data-guests="data-guests">
                    <div class="field__input" data-guests-control="data-guests-control"><?=$guests + $children?> <?= $guestsDeclension->get($guests + $children) ?></div>

                    <div class="guests__dropdown">
                        <div class="guests__guests">
                            <div class="guests__item">
                                <div class="guests__label">
                                    <div>Взрослые</div><span>от 18 лет</span>
                                </div>
                                <div class="counter">
                                    <button class="counter__minus" type="button"></button>
                                    <input type="text" disabled="disabled" data-guests-adults-count="data-guests-adults-count" name="guests-adults-count" value="<?=$guests?>" data-min="1">
                                    <button class="counter__plus" type="button"></button>
                                </div>
                            </div>

                            <div class="guests__item">
                                <div class="guests__label">
                                    <div>Дети</div><span>от 0 до 17 лет</span>
                                </div>
                                <div class="counter">
                                    <button class="counter__minus" type="button"></button>
                                    <input type="text" disabled="disabled" data-guests-children-count="data-guests-children-count" name="guests-children-count" value="<?=$children?>" data-min="0">
                                    <button class="counter__plus" type="button"></button>
                                </div>
                            </div>
                        </div>

                        <div class="guests__children" data-guests-children="data-guests-children">
                            <? if ($arChildrenAge): ?>
                                <? foreach ($arChildrenAge as $keyAge => $valueAge): ?>
                                    <div class="guests__item">
                                        <div class="guests__label">
                                            <div>Возраст</div>
                                            <span><?=getChildrenOrderTitle($keyAge + 1)?> ребенка</span>
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
                        <li class="list__item" >
                            <label class="checkbox">
                                <input type="checkbox" name="type" <?= $onclick; ?> value="<?= $arType["ID"] ?>" <? if ($arFilterTypes && in_array($arType["ID"], $arFilterTypes)) : ?>checked<? endif; ?>><span><?= $arType["UF_NAME"] ?></span>
                            </label>
                        </li>
                    <? endforeach; ?>
                </ul>
            </div>
        </div>

        <?if($arServices):?>
            <div class="form__dropdown form__dropdown_show">
                <div class="form__dropdown-heading h6">Окружение</div>
                <div class="form__dropdown-body">
                    <ul class="list list_checkboxes">
                        <? foreach ($arServices as $arService) : ?>
                            <li class="list__item">
                                <label class="checkbox">
                                    <input type="checkbox" name="services" value="<?= $arService["ID"] ?>" <? if ($arFilterServices && in_array($arService["ID"], $arFilterServices)) : ?>checked<? endif; ?>><span><?= $arService["NAME"] ?></span>
                                </label>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>
        <?endif;?>

        <?if($arHLFood):?>
            <div class="form__dropdown form__dropdown_show">
                <div class="form__dropdown-heading h6">Питание</div>
                <div class="form__dropdown-body">
                    <ul class="list list_checkboxes">
                        <? foreach ($arHLFood as $arFoodItem) : ?>
                            <li class="list__item">
                                <label class="checkbox">
                                    <input type="checkbox" name="food" value="<?= $arFoodItem["ID"] ?>" <? if ($arFilterFood && in_array($arFoodItem["ID"], $arFilterFood)) : ?>checked<? endif; ?>><span><?= $arFoodItem["UF_NAME"] ?></span>
                                </label>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>
        <?endif;?>

        <?if($arHLFeatures):?>
            <div class="form__dropdown form__dropdown_show">
                <div class="form__dropdown-heading h6">Особенности</div>
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
                                           <? if ($arFilterFeatures && in_array($arFeature["ID"],
                                               $arFilterFeatures)) : ?>checked<? endif; ?>><span><?= $arFeature["UF_NAME"] ?></span>
                                </label>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>
        <?endif;?>
    </div>

    <div class="filters__controls">
        <button class="button button_clear" data-filter-reset>Сбросить всё</button>
        <button class="button button_primary" data-filter-set>Подобрать</button>
    </div>
</form>