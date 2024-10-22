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

<div>
    <? foreach ($filterData as $filterDatum) { ?>
        <div class="object-row__cert-price">
            <span><?= $filterDatum['NAME']?></span>
            <span></span>
        </div>
    <? } ?>

</div>
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
                if (empty($arSection["ID"])) {
                    continue;
                }

                $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="object-row" target="_blank" data-map-id="<?= $arSection["ID"] ?>" href="<?= $arSection["URL"] ?>"
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
                                        <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                            data-src="<?= $arPhoto["src"] ?>">
                                    </div>
                                    <?php $keyPhoto++; ?>
                                    <?php $keyPhotoFullGallery++; ?>
                                <?php endforeach; ?>

                            </div>

                            <?php if ($arSection["PICTURES"] && sizeof($arSection["PICTURES"]) > 1) : ?>
                                <div class="swiper-button-prev">
                                    <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                        style="width: 1.6rem; height: 1.6rem;">
                                        <use xlink:href="#arrow-small" />
                                    </svg>
                                </div>
                                <div class="swiper-button-next">
                                    <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                        style="width: 1.6rem; height: 1.6rem;">
                                        <use xlink:href="#arrow-small" />
                                    </svg>
                                </div>
                                <div class="swiper-pagination"></div>
                            <?php endif; ?>
                        </div>

                        <button class="favorite "
                            <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<?php else: ?>data-favourite-add<?php endif; ?> data-id="<?= $arSection["ID"] ?>">
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z" fill="#E39250" />
                            </svg>
                        </button>

                        <?php if ($arSection["IS_DISCOUNT"] == 'Y'): ?>
                            <div class="tag"><?= $arSection["UF_SALE_LABEL"] != '' ? $arSection["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?> <?= $arSection["DISCOUNT_PERCENT"] ? $arSection["DISCOUNT_PERCENT"] . '%' : '' ?></div>
                        <?php endif; ?>

                        <?php if (!empty($arSection["UF_ACTION"])) : ?>
                            <div class="tag"><?= $arSection["UF_ACTION"] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="object-row__content">
                        <div class="object-row__description">
                            <a class="object-row__title h3" target="_blank"
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
                                <a target="_blank" href="<?= $arSection["URL"] ?>#reviews-anchor"
                                    style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score"
                                    data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][1][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][2][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][3][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][4][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][5][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][6][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][7][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][8][0] ?? '0.0' ?>}]">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                                    <span><?= $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0 ?></span>
                                </a>
                                <a target="_blank" href="<?= $arSection["URL"] ?>#reviews-anchor"><?= $arReviewsAvg[$arSection["ID"]]["count"] ?? 0 ?> <?= $reviewsDeclension->get($arReviewsAvg[$arSection["ID"]]["count"]) ?></a>
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

                                    <?php if ($USER->IsAdmin()): ?>
                                        <?php if (
                                            $arSection["PRICE"] > Users::getInnerScore()
                                            && intval(Users::getInnerScore()) !== 0
                                            && $isAuthorized
                                        ): ?>
                                            <div class="object-row__cert-price">
                                                <span>Доплата</span>
                                                <span>
                                                    <?= number_format($arSection["PRICE"] - Users::getInnerScore(), 0, '.', ' ') ?>₽
                                                </span>
                                            </div>
                                        <? endif; ?>
                                    <? endif; ?>

                                <?php endif; ?>
                            </div>

                            <a class="button button_primary"
                                target="_blank"
                                onclick="VK.Goal('customize_product');"
                                href="<?= $arSection["URL"] ?>">Выбрать</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="catalog__more">
            <?
            $nav = new \Bitrix\Main\UI\PageNavigation("page");
            $nav->allowAllRecords(false)
                ->setPageSize($arResult['itemsCount'])
                ->initFromUri();
            $nav->setRecordCount($arResult['allCount']);

            $APPLICATION->IncludeComponent(
                "bitrix:main.pagenavigation",
                "modern",
                array(
                    "NAV_OBJECT" => $nav,
                    "SEF_MODE" => "N",
                ),
                false
            );
            ?>
        </div>

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
                                                <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                                    data-src="<?= $arPhoto["src"] ?>">
                                            </div>
                                            <?php $keyPhoto++; ?>
                                            <?php $keyPhotoFullGallery++; ?>
                                        <?php endforeach; ?>

                                    </div>

                                    <?php if ($arSection["PICTURES"] && sizeof($arSection["PICTURES"]) > 1) : ?>
                                        <div class="swiper-button-prev">
                                            <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                                style="width: 1.6rem; height: 1.6rem;">
                                                <use xlink:href="#arrow-small" />
                                            </svg>
                                        </div>
                                        <div class="swiper-button-next">
                                            <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                                style="width: 1.6rem; height: 1.6rem;">
                                                <use xlink:href="#arrow-small" />
                                            </svg>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    <?php endif; ?>
                                </div>

                                <button class="favorite<?= ($arFavourites && in_array($arSection["ID"], $arFavourites)) ? ' active' : '' ?>"
                                    <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<?php else: ?>data-favourite-add<?php endif; ?> data-id="<?= $arSection["ID"] ?>">
                                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z" fill="#E39250" />
                                    </svg>
                                </button>

                                <?php if ($arSection["IS_DISCOUNT"] == 'Y'): ?>
                                    <div class="tag"><?= $arSection["UF_SALE_LABEL"] != '' ? $arSection["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?> <?= $arSection["DISCOUNT_PERCENT"] ? $arSection["DISCOUNT_PERCENT"] . '%' : '' ?></div>
                                <?php endif; ?>

                                <?php if (!empty($arSection["UF_ACTION"])) : ?>
                                    <div class="tag"><?= $arSection["UF_ACTION"] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="object-row__content">
                                <div class="object-row__description">
                                    <a class="object-row__title h3" target="_blank"
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
                                        <a target="_blank" href="<?= $arSection["URL"] ?>#reviews-anchor"
                                            style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score"
                                            data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][1][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][2][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][3][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][4][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][5][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][6][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][7][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][8][0] ?? '0.0' ?>}]">
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                                            <span><?= $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0 ?></span>
                                        </a>
                                        <a target="_blank" href="<?= $arSection["URL"] ?>#reviews-anchor"><?= $arReviewsAvg[$arSection["ID"]]["count"] ?? 0 ?> <?= $reviewsDeclension->get($arReviewsAvg[$arSection["ID"]]["count"]) ?></a>
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

                                            <?php if ($USER->IsAdmin()): ?>
                                                <?php if (
                                                    $arSection["PRICE"] > Users::getInnerScore()
                                                    && intval(Users::getInnerScore()) !== 0
                                                    && $isAuthorized
                                                ): ?>
                                                    <div class="object-row__cert-price">
                                                        <span>Доплата</span>
                                                        <span>
                                                            <?= number_format($arSection["PRICE"] - Users::getInnerScore(), 0, '.', ' ') ?>₽
                                                        </span>
                                                    </div>
                                                <? endif; ?>
                                            <? endif; ?>

                                        <?php endif; ?>
                                    </div>

                                    <a class="button button_primary"
                                        target="_blank"
                                        onclick="VK.Goal('customize_product');"
                                        href="<?= $arSection["URL"] ?>">Выбрать</a>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php } ?>
        </div>

    </div>

    <div class="catalog__map <?= (CSite::InDir('/map')) ? 'catalog__on_map' : '' ?>" data-map-overlay>
        <div class="catalog__map-sticky">
            <div id="map"></div>

            <?/*button class="catalog__map-fullscreen" data-map-full type="button">
                <svg class="icon icon_fullscreen" viewbox="0 0 20 20" style="width: 2rem; height: 2rem;">
                    <use xlink:href="#fullscreen" />
                </svg>
            </button*/ ?>
            <?php if (CSite::InDir('/map')): ?>
                <a href="/catalog/" class="button button_primary catalog__map-halfscreen link__to_catalog">
                    <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                        <use xlink:href="#arrow-text" />
                    </svg>
                    <span>Перейти к списку</span>
                </a>
                <?/*a class="button button_primary  link_route" target="_blank"
                    href="https://yandex.ru/maps/?mode=routes&rtext=" data-route="data-route">Маршрут</a*/ ?>
            <?php endif; /*?>
                <button class="button button_primary catalog__map-halfscreen" data-map-half type="button">
                    <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                        <use xlink:href="#arrow-text" />
                    </svg>
                    <span>Перейти к списку</span>
                </button>
            <?php endif; */ ?>
            <div class="catalog__map-more" data-map-more-wrapper></div>
        </div>
    </div>
</div>