<?php

use Bitrix\Main\Localization\Loc;
use Naturalist\Users;

global $isAuthorized;

Loc::loadMessages(__FILE__);

/** @var  $arResult */

foreach ($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="catalog">

    <div class="catalog__objects" data-catalog-container>


        <?php if ($allCount > 0) {
            ?>
            <div class="catalog__count">Доступно <?= $allCount ?> <?= $countDeclension->get($allCount) ?></div>
            <?php
        } else {
            if (isset($_GET['name'])) { ?>
                <div class="catalog__count--not-found"><?= Loc::GetMessage('NOT_FOUND_REGION') ?></div>
            <?php } else { ?>
                <div class="catalog__count--not-found"><?= Loc::GetMessage('NOT_FOUND') ?></div>
                <?php
            }
        } ?>


        <div class="catalog__list">
            <?php foreach ($arPageSections as $arSection) : ?>
                <?php
                if(empty($arSection["ID"])) {
                    continue;
                }
                
                $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="object-row" data-map-id="<?= $arSection["ID"] ?>" href="<?= $arSection["URL"] ?>" 
                     id="<?= $this->GetEditAreaId($arSection['ID']) ?>">
                    <div class="object-row__images">
                        <div class="swiper slider-gallery" data-slider-object="data-slider-object"
                             data-fullgallery="[<?= $arSection["FULL_GALLERY"]; ?>]">
                            <div class="swiper-wrapper">

                                <?php $keyPhoto = 1; ?>
                                <?php $keyPhotoFullGallery = 0; ?>
                                <?php foreach ($arSection["PICTURES"] as $arPhoto) : ?>
                                    <?php if (count($arSection["PICTURES"]) > 1): ?>
                                        <?php
                                        $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " рис." . $keyPhoto;
                                        $title = "Фото - " . $arSection["NAME"] . " рис." . $keyPhoto;
                                        ?>
                                    <?php else: ?>
                                        <?php
                                        $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"];
                                        $title = "Фото - " . $arSection["NAME"];
                                        ?>

                                    <?php endif; ?>
                                    <div class="swiper-slide" data-fullgallery-item="<?= $keyPhotoFullGallery; ?>">
                                        <img class="" loading="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                             src="<?= $arPhoto["src"] ?>">
                                    </div>
                                    <?php $keyPhoto++; ?>
                                    <?php $keyPhotoFullGallery++; ?>
                                <?php endforeach; ?>

                            </div>

                            <?php if ($arSection["PICTURES"] && sizeof($arSection["PICTURES"]) > 1) : ?>
                                <div class="swiper-button-prev">
                                    <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                         style="width: 1.6rem; height: 1.6rem;">
                                        <use xlink:href="#arrow-small"/>
                                    </svg>
                                </div>
                                <div class="swiper-button-next">
                                    <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                         style="width: 1.6rem; height: 1.6rem;">
                                        <use xlink:href="#arrow-small"/>
                                    </svg>
                                </div>
                                <div class="swiper-pagination"></div>
                            <?php endif; ?>
                        </div>

                        <button class="favorite"
                                <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<?php else: ?>data-favourite-add<?php endif; ?>data-id="<?= $arSection["ID"] ?>">
                            <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg" alt>
                            <?php else : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt>
                            <?php endif; ?>
                        </button>

                        <?php if ($arSection["IS_DISCOUNT"] == 'Y'): ?>
                            <div class="tag"><?= $arSection["UF_SALE_LABEL"] != '' ? $arSection["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?></div>
                        <?php endif; ?>

                        <?php if (!empty($arSection["UF_ACTION"])) : ?>
                            <div class="tag"><?= $arSection["UF_ACTION"] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="object-row__content">
                        <div class="object-row__description">
                            <a class="object-row__title h3" onclick="setLocalStorageCatalog(event);"
                               href="<?= $arSection["URL"] ?>"><?= $arSection["NAME"] ?></a>

                            <div class="area-info">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt>
                                <div>
                                    <?php if (isset($arHLTypes[$arSection["UF_TYPE"]])) : ?>
                                        <span><?= $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] ?></span><?php endif; ?>
                                    <? /*php if (!empty($arSection["UF_DISTANCE"])) : ?><span><?= $arSection["UF_DISTANCE"] ?></span><?php endif; */ ?>
                                    <? /*php if (!empty($arSection["UF_ADDRESS"])) : ?><span><?= $arSection["UF_ADDRESS"] ?></span><?php endif; */ ?>
                                    <?php if (!empty($arSection["DISCTANCE"])) { ?>
                                        <span>
                                        , <?= $arSection["DISCTANCE"] ?> км
                                        от <?= $arSection['DISCTANCE_TO_REGION'] ?>
                                        </span>
                                    <?php  } else {
                                        if (is_array($arSection["REGION"]) && count($arSection["REGION"]) > 0) { ?>
                                    <span>, <?= $arSection["REGION"]['UF_NAME'] ?></span>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                            <div class="object-row__reviews">
                                <a href="<?= $arSection["URL"] ?>#reviews-anchor"
                                   style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score"
                                   data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][1][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][2][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][3][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][4][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][5][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][6][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][7][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][8][0] ?? '0.0' ?>}]">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                                    <span><?= $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0 ?></span>
                                </a>
                                <a href="<?= $arSection["URL"] ?>#reviews-anchor"><?= $arReviewsAvg[$arSection["ID"]]["count"] ?? 0 ?> <?= $reviewsDeclension->get($arReviewsAvg[$arSection["ID"]]["count"]) ?></a>
                            </div>

                            <?php if ($arSection["UF_FEATURES"]): ?>
                                <div class="object-row__features">
                                    <?php
                                    $featureCounter = 0;
                                    foreach ($arSection["UF_FEATURES"] as $featureId) :
                                        if (empty($arHLFeatures[$featureId]["UF_NAME"])) {
                                            continue;
                                        }
                                        $featureCounter++;
                                        if ($featureCounter > 6) {
                                            continue;
                                        }
                                        ?>
                                        <span><?= $arHLFeatures[$featureId]["UF_NAME"] ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="object-row__order">
                            <div class="object-row__price">
                                <?php if ($arSection["PRICE"] > 0): ?>

                                    <div class="object-row__price_wrapper">
                                        <div><?= number_format($arSection["PRICE"], 0, '.', ' ') ?> ₽</div>
                                        <span>Цена за одну ночь</span>
                                    </div>

                                    <?php if ($USER->IsAdmin()):?>
                                        <?php if (
                                                $arSection["PRICE"] > Users::getInnerScore()
                                                && intval(Users::getInnerScore()) !== 0
                                                && $isAuthorized
                                            ): ?>
                                            <div class="object-row__cert-price">
                                                <span>Доплата</span>
                                                <span>
                                                    <?=number_format($arSection["PRICE"] - Users::getInnerScore(), 0, '.', ' ')?>₽
                                                </span>
                                            </div>
                                        <? endif; ?>
                                    <? endif; ?>

                                <?php endif; ?>
                            </div>

                            <a class="button button_primary"
                               onclick="VK.Goal('customize_product');setLocalStorageCatalog(event);"
                               href="<?= $arSection["URL"] ?>">Выбрать</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($page < $pageCount) : ?>
            <div class="catalog__more">
                <a href="#" data-catalog-showmore data-page="<?= $page + 1 ?>">Показать ещё</a>
            </div>
        <?php endif; ?>

        <div id="same_items" style="<?= ($page < $pageCount) ? 'display:none;' : 'margin-top: 24px;' ?>">

            <?php if ($arResult['arSearchedRegions'] && is_array($arResult["SECTIONS"]) && count($arResult["SECTIONS"]) > 0) { ?>

                <?php if ($allCount > 0) { ?>
                    <div class="same_items-header">
                    <?= Loc::GetMessage('SAME_ITEMS_HEAD') ?>
                    </div>
                <?php } ?>

                <div class="same_items-body">
                    <?php foreach ($arResult["SECTIONS"] as $arSection): ?>
                        <?php
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                        ?>

                        <div class="object-row" data-map-id="<?= $arSection["ID"] ?>" href="<?= $arSection["URL"] ?>" 
                             id="<?= $this->GetEditAreaId($arSection['ID']) ?>">
                            <div class="object-row__images">
                                <div class="swiper slider-gallery" data-slider-object="data-slider-object"
                                     data-fullgallery="[<?= $arSection["FULL_GALLERY"]; ?>]">
                                    <div class="swiper-wrapper">

                                        <?php $keyPhoto = 1; ?>
                                        <?php $keyPhotoFullGallery = 0; ?>
                                        <?php foreach ($arSection["PICTURES"] as $arPhoto) : ?>
                                            <?php if (count($arSection["PICTURES"]) > 1): ?>
                                                <?php
                                                $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " рис." . $keyPhoto;
                                                $title = "Фото - " . $arSection["NAME"] . " рис." . $keyPhoto;
                                                ?>
                                            <?php else: ?>
                                                <?php
                                                $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"];
                                                $title = "Фото - " . $arSection["NAME"];
                                                ?>

                                            <?php endif; ?>
                                            <div class="swiper-slide"
                                                 data-fullgallery-item="<?= $keyPhotoFullGallery; ?>">
                                                <img class="" loading="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                                     src="<?= $arPhoto["src"] ?>">
                                            </div>
                                            <?php $keyPhoto++; ?>
                                            <?php $keyPhotoFullGallery++; ?>
                                        <?php endforeach; ?>

                                    </div>

                                    <?php if ($arSection["PICTURES"] && sizeof($arSection["PICTURES"]) > 1) : ?>
                                        <div class="swiper-button-prev">
                                            <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                                 style="width: 1.6rem; height: 1.6rem;">
                                                <use xlink:href="#arrow-small"/>
                                            </svg>
                                        </div>
                                        <div class="swiper-button-next">
                                            <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                                 style="width: 1.6rem; height: 1.6rem;">
                                                <use xlink:href="#arrow-small"/>
                                            </svg>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    <?php endif; ?>
                                </div>

                                <button class="favorite"
                                        <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<?php else: ?>data-favourite-add<?php endif; ?>data-id="<?= $arSection["ID"] ?>">
                                    <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg" alt>
                                    <?php else : ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt>
                                    <?php endif; ?>
                                </button>

                                <?php if ($arSection["IS_DISCOUNT"] == 'Y'): ?>
                                    <div class="tag"><?= $arSection["UF_SALE_LABEL"] != '' ? $arSection["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?></div>
                                <?php endif; ?>

                                <?php if (!empty($arSection["UF_ACTION"])) : ?>
                                    <div class="tag"><?= $arSection["UF_ACTION"] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="object-row__content">
                                <div class="object-row__description">
                                    <a class="object-row__title h3" onclick="setLocalStorageCatalog(event);"
                                       href="<?= $arSection["URL"] ?>"><?= $arSection["NAME"] ?></a>

                                    <div class="area-info">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt>
                                        <div>
                                            <?php if (isset($arHLTypes[$arSection["UF_TYPE"]])) : ?>
                                                <span><?= $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] ?></span><?php endif; ?>
                                            <? /*php if (!empty($arSection["UF_DISTANCE"])) : ?><span><?= $arSection["UF_DISTANCE"] ?></span><?php endif; */ ?>
                                            <? /*php if (!empty($arSection["UF_ADDRESS"])) : ?><span><?= $arSection["UF_ADDRESS"] ?></span><?php endif; */ ?>
                                            <?php if (!empty($arSection["DISCTANCE"])) : ?><span>
                                                , <?= $arSection["DISCTANCE"] ?> км
                                                от <?= $arSection['DISCTANCE_TO_REGION'] ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="object-row__reviews">
                                        <a href="<?= $arSection["URL"] ?>#reviews-anchor"
                                           style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score"
                                           data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][1][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][2][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][3][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][4][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][5][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][6][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][7][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][8][0] ?? '0.0' ?>}]">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                                            <span><?= $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0 ?></span>
                                        </a>
                                        <a href="<?= $arSection["URL"] ?>#reviews-anchor"><?= $arReviewsAvg[$arSection["ID"]]["count"] ?? 0 ?> <?= $reviewsDeclension->get($arReviewsAvg[$arSection["ID"]]["count"]) ?></a>
                                    </div>

                                    <?php if ($arSection["UF_FEATURES"]): ?>
                                        <div class="object-row__features">
                                            <?php
                                            $featureCounter = 0;
                                            foreach ($arSection["UF_FEATURES"] as $featureId) :
                                                if (empty($arHLFeatures[$featureId]["UF_NAME"])) {
                                                    continue;
                                                }
                                                $featureCounter++;
                                                if ($featureCounter > 6) {
                                                    continue;
                                                }
                                                ?>
                                                <span><?= $arHLFeatures[$featureId]["UF_NAME"] ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="object-row__order">
                                    <div class="object-row__price">
                                        <?php if ($arSection["PRICE"] > 0): ?>
                                            <div class="object-row__price_wrapper">
                                                <div><?= number_format($arSection["PRICE"], 0, '.', ' ') ?> ₽</div>
                                                <span>Цена за одну ночь</span>
                                            </div>

                                            <?php if ($USER->IsAdmin()):?>
                                                <?php if (
                                                        $arSection["PRICE"] > Users::getInnerScore()
                                                        && intval(Users::getInnerScore()) !== 0
                                                        && $isAuthorized
                                                    ): ?>
                                                    <div class="object-row__cert-price">
                                                        <span>Доплата</span>
                                                        <span>
                                                            <?=number_format($arSection["PRICE"] - Users::getInnerScore(), 0, '.', ' ')?>₽
                                                        </span>
                                                    </div>
                                                <? endif; ?>
                                            <? endif; ?>

                                        <?php endif; ?>
                                    </div>

                                    <a class="button button_primary"
                                       onclick="VK.Goal('customize_product');setLocalStorageCatalog(event);"
                                       href="<?= $arSection["URL"] ?>">Выбрать</a>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php } ?>
        </div>

    </div>

    <div class="catalog__map <?php if (CSite::InDir('/map')): ?>catalog__on_map<?php endif; ?>" data-map-overlay>
        <div class="catalog__map-sticky">
            <div id="map"></div>

            <button class="catalog__map-fullscreen" data-map-full type="button">
                <svg class="icon icon_fullscreen" viewbox="0 0 20 20" style="width: 2rem; height: 2rem;">
                    <use xlink:href="#fullscreen"/>
                </svg>
            </button>
            <?php if (CSite::InDir('/map')): ?>
                <a href="/catalog/" class="button button_primary catalog__map-halfscreen link__to_catalog">
                    <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                        <use xlink:href="#arrow-text"/>
                    </svg>
                    <span>Перейти к списку</span>
                </a>
                <a class="button button_primary  link_route" target="_blank"
                   href="https://yandex.ru/maps/?mode=routes&rtext=" data-route="data-route">Маршрут</a>
            <?php else: ?>
                <button class="button button_primary catalog__map-halfscreen" data-map-half type="button">
                    <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                        <use xlink:href="#arrow-text"/>
                    </svg>
                    <span>Перейти к списку</span>
                </button>
            <?php endif; ?>
            <div class="catalog__map-more" data-map-more-wrapper></div>
        </div>
    </div>
</div>
