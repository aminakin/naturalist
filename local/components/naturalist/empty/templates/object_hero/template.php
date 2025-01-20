<?
foreach ($arResult as $key => $value) {
    ${$key} = $value;
}

global $isMobile;
?>
<section class="section section_object">
    <div class="container">
        <div class="object-hero">
            <div class="object__top">
                <div class="object-hero__heading">
                    <h1><?= htmlspecialcharsBack($arParams["h1SEO"]); ?></h1>
                    <? if ($reviewsCount > 0): ?>
                        <div class="object-hero__reviews">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                            <span><?= $avgRating ?></span>
                        </div>
                    <? endif; ?>
                </div>
                <div class="area-info">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt width="20" height="20">
                    <? if (!empty($arSection["UF_ADDRESS"])) : ?>
                        <span><?= htmlspecialcharsBack($arSection["UF_ADDRESS"]) ?></span>
                    <? endif; ?>
                </div>
                <button class="favorite top <?= ($arFavourites && in_array($arSection["ID"], $arFavourites)) ? ' active' : '' ?>"
                    <? if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<? else: ?>data-favourite-add<? endif; ?>
                    data-id="<?= $arSection["ID"] ?>">
                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z" fill="#E39250"></path>
                    </svg>
                </button>
            </div>
            <div class="object-hero__gallery">
                <? if ($isMobile) { ?>
                    <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $arSection["FULL_GALLERY"]; ?>]">
                        <div class="swiper-wrapper">
                            <? $keyPhoto = 1; ?>
                            <? $keyPhotoFullGallery = 0; ?>
                            <? foreach ($arSection["PICTURES"] as $arPhoto) : ?>
                                <? if (count($arSection["PICTURES"]) > 1): ?>
                                    <?
                                    $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " " . $arSection["UF_REGION_NAME"] . " рис." . $keyPhoto;;
                                    $title = "Фото - " . $arSection["NAME"] . " рис." . $keyPhoto;
                                    ?>
                                <? else: ?>
                                    <?
                                    $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " " . $arSection["UF_REGION_NAME"];
                                    $title = "Фото - " . $arSection["NAME"];
                                    ?>

                                <? endif; ?>
                                <div class="swiper-slide" data-fullgallery-item="<?= $keyPhotoFullGallery; ?>">
                                    <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                        data-src="<?= $arPhoto["src"] ?>">
                                </div>
                                <? $keyPhoto++; ?>
                                <? $keyPhotoFullGallery++; ?>
                            <? endforeach; ?>
                        </div>

                        <? if (count($arSection["PICTURES"]) > 1) : ?>
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
                <? } else { ?>
                    <div class="object__galery <?= count($arSection["PICTURES"]) == 1 ? 'alone' : '' ?>">
                        <div class="object__galery-item first">
                            <img src="<?= $arSection["PICTURES"][0]['src'] ?>" loading="lazy" alt="">
                        </div>
                        <? if (count($arSection["PICTURES"]) > 1) { ?>
                            <div class="object__galery-item">
                                <img src="<?= $arSection["PICTURES"][1]['src'] ?>" loading="lazy" alt="">
                            </div>
                            <div class="object__galery-item">
                                <img src="<?= $arSection["PICTURES"][2]['src'] ?>" loading="lazy" alt="">
                                <span><?= count($arSection["PICTURES"]) ?> фото</span>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
            </div>

            <div class="object__detail-info">
                <div class="object__info-left">
                    <? if (!empty($arSection["DESCRIPTION"])) { ?>
                        <div class="object__description">
                            <div class="about__text about__text_hidden">
                                <div class="about__text-content">
                                    <div class="h3">Об этом месте</div>
                                    <div class="about__text-hide" data-text-show>
                                        <div><?= htmlspecialcharsBack($arSection["~DESCRIPTION"]) ?></div>
                                    </div>
                                </div>

                                <div class="about__text-show">
                                    <a href="#" data-text-show-control data-text-show-more="Развернуть" data-text-show-hide="Скрыть"></a>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    <div class="about">
                        <? if ($arSection["UF_FEATURES"]): ?>
                            <div class="about__text about__text_hidden">
                                <? if ($arSection["UF_FEATURES"]): ?>
                                    <ul class="list list_icons">
                                        <? foreach ($arSection["UF_FEATURES"] as $key => $featureId): ?>
                                            <?
                                            $arIcon = CFile::GetFileArray($arHLFeatures[$featureId]["UF_ICON"]);
                                            ?>
                                            <li class="list__item">
                                                <img src="<?= $arIcon["SRC"] ?>" alt="<?= $arHLFeatures[$featureId]["UF_NAME"] ?>">
                                                <span><?= $arHLFeatures[$featureId]["UF_NAME"] ?></span>
                                            </li>
                                        <? endforeach; ?>
                                    </ul>
                                <? endif; ?>
                            </div>
                        <? endif; ?>
                        <? if ($arServices): ?>
                            <div class="about__services">
                                <div class="about__services-list">
                                    <? foreach ($arServices as $arServiceGroup): ?>
                                        <div class="about__services-item">
                                            <div class="h6"><?= $arServiceGroup["NAME"] ?></div>
                                            <ul class="list">
                                                <? foreach ($arServiceGroup["ITEMS"] as $arServiceItem): ?>
                                                    <li class="list__item"><?= $arServiceItem["NAME"] ?></li>
                                                <? endforeach; ?>
                                            </ul>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>
                    <div class="object-hero__form">
                        <form class="form" id="form-object-filter">
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
                                            data-date-to><? if ($dateTo): ?><?= $dateTo ?><? else: ?><span>Выезд</span><? endif; ?></div>
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
                                                <li class="list__item">
                                                    <div class="list__item-year"><?= $nextYear ?></div>
                                                </li>
                                                <? foreach ($arDates[1] as $monthName) : ?>
                                                    <li class="list__item" data-calendar-delimiter="data-calendar-delimiter">
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
                                                data-calendar-navigation="data-calendar-navigation"><span><?= $currYear ?></span>
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

                            <div class="form__row">
                                <div class="form__item">
                                    <div class="field field_icon guests" data-guests="data-guests">
                                        <div class="field__input"
                                            data-guests-control="data-guests-control"><?= $guests + $children ?> <?= $guestsDeclension->get($guests + $children) ?></div>
                                        <div class="guests__dropdown">
                                            <div class="guests__guests">
                                                <div class="guests__item">
                                                    <div class="guests__label">
                                                        <div><?= GetMessage('FILTER_ADULTS') ?></div><span><?= GetMessage('FILTER_ADULTS_AGE') ?></span>
                                                    </div>
                                                    <div class="counter">
                                                        <button class="counter__minus" type="button"></button>
                                                        <input type="text" disabled="disabled"
                                                            data-guests-adults-count="data-guests-adults-count"
                                                            name="guests-adults-count" value="<?= $guests ?>" data-min="1"
                                                            data-max="99">
                                                        <button class="counter__plus" type="button"></button>
                                                    </div>
                                                </div>

                                                <div class="guests__item">
                                                    <div class="guests__label">
                                                        <div><?= GetMessage('FILTER_CHILDREN') ?></div><span><?= GetMessage('FILTER_CHILDREN_AGE') ?></span>
                                                    </div>
                                                    <div class="counter">
                                                        <button class="counter__minus" type="button"></button>
                                                        <input type="text" disabled="disabled"
                                                            data-guests-children-count="data-guests-children-count"
                                                            name="guests-children-count" value="<?= $children ?>" data-min="0"
                                                            data-max="20">
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
                                </div>

                                <div class="form__item">
                                    <button class="button button_primary" data-object-filter-set>Найти</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="object__info-right" id="map">
                    <? if ($coords): ?>
                        <div class="about__map">
                            <div class="about__map-heading">
                                <a href="https://yandex.ru/maps/?mode=routes&rtext=~<?= $coords ?>" target="_blank">Проложить маршрут</a>
                                <div class="object__marker-map">
                                    <a href="#modal-map" data-modal>На карте</a>
                                </div>
                            </div>

                            <div class="about__map-map">
                                <div id="map-preview"></div>
                                <a class="about__map-modal" href="#modal-map" data-modal></a>
                            </div>
                        </div>
                    <? endif; ?>
                    <? if ($reviewsCount > 0): ?>
                        <div class="object-hero__reviews">
                            <a href="#reviews-anchor" data-scroll-to class="score">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt=""><span><?= $avgRating ?></span>
                            </a>
                            <a href="#reviews-anchor"
                                data-scroll-to=""><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?></a>
                        </div>
                        <div class="reviews__scors">
                            <div class="reviews__title">Что нравится гостям</div>
                            <ul class="list list_score">
                                <li class="list__item">
                                    <div class="list__item-label">Удобство расположения</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[1][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[1][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Питание</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[2][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[2][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Уют</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[3][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[3][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Сервис</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[4][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[4][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Чистота</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[5][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[5][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Эстетика окружения</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[6][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[6][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Разнообразие досуга</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[7][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[7][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Соотношение цена/качество</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[8][1] ?? 0 ?>%"></div>
                                    </div>
                                    <div class="list__item-number"><?= $arAvgCriterias[8][0] ?? "0.0" ?></div>
                                </li>
                            </ul>
                        </div>
                    <? endif; ?>
                </div>
            </div>

            <div class="object-hero__description">
                <?/*<div class="object-hero__heading">
                    <div class="object-hero__controls">
                        <div class="share">
                            <button class="share__control" type="button" data-share="data-share">
                                <svg class="icon icon_share" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                    <use xlink:href="#share" />
                                </svg>
                            </button>
                            <div class="share__dropdown">
                                <div class="share__copy">
                                    <button type="button" data-copy-link="<?= $currentURL ?>">Копировать ссылку</button>
                                </div>
                                <ul class="list">
                                    <li class="list__item">
                                        <a class="list__link"
                                            href="https://t.me/share/url?url=<?= $currentURL ?>&text=<?= $arSection["NAME"] ?>"
                                            target="_blank">
                                            <span class="list__item-icon">
                                                <svg class="icon icon_telegram" viewbox="0 0 16 16"
                                                    style="width: 1.6rem; height: 1.6rem;">
                                                    <use xlink:href="#telegram" />
                                                </svg>
                                            </span>
                                            <span class="list__item-title">Telegram</span>
                                        </a>
                                    </li>
                                    <li class="list__item">
                                        <a class="list__link" href="https://vk.com/share.php?url=<?= $currentURL ?>"
                                            target="_blank">
                                            <span class="list__item-icon">
                                                <svg class="icon icon_vk" viewbox="0 0 22 12"
                                                    style="width: 2.2rem; height: 1.2rem;">
                                                    <use xlink:href="#vk" />
                                                </svg>
                                            </span>
                                            <span class="list__item-title">Вконтакте</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>*/ ?>

                <?/*<div class="object-hero__marker">
                    <? if ($arSection["COORDS"]): ?>
                        
                    <? endif; ?>
                </div>*/ ?>



                <div class="object-hero__text">
                    <p><?= $arSection["UF_PREVIEW_TEXT"] ?></p>
                </div>


            </div>
        </div>
    </div>
</section>

<script>
    dataLayer.push({
        "ecommerce": {
            "currencyCode": "RUB",
            "detail": {
                "products": [{
                    "id": "<?= $arSection['UF_EXTERNAL_ID'] ?>",
                    "name": "<?= $arSection['NAME'] ?>",
                    "price": <?= $arSection['UF_MIN_PRICE'] ?>,
                    "brand": "<?= $arSection['UF_EXTERNAL_SERVICE'] ?>",
                }]
            }
        }
    });
</script>