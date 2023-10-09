<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/** @var  $arResult */

foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="catalog">

    <div class="catalog__objects" data-catalog-container>

        <div class="sort">
            <span>Сортировать по:</span>
            <ul class="list">
                <li class="list__item">
                    <?php if($sortBy == "sort"):?>
                        <span class="list__link" data-sort="sort" data-type="<?=$orderReverse?>"><span>По</span> <span>Наитию</span></span>
                    <?php else:?>
                        <a class="list__link" href="#" data-sort="sort" data-type="asc"><span>По</span> <span>Наитию</span></a>
                    <?php endif;?>
                </li>
                <!--<li class="list__item">
                    <?php /*if($sortBy == "popular"):*/?>
                        <span class="list__link" data-sort="popular" data-type="<?php /*=$orderReverse*/?>"><span>По</span> <span>Популярности</span></span>
                    <?php /*else:*/?>
                        <a class="list__link" href="#" data-sort="popular" data-type="<?php /*=$orderReverse*/?>"><span>По</span> <span>Популярности</span></a>
                    <?php /*endif;*/?>
                </li>-->
                <li class="list__item">
                    <?php if($sortBy == "price"):?>
                        <span class="list__link" data-sort="price" data-type="<?=$orderReverse?>"><span>По</span> <span>Цене</span></span>
                    <?php else:?>
                        <a class="list__link" href="#" data-sort="price" data-type="asc"><span>По</span> <span>Цене</span></a>
                    <?php endif;?>
                </li>
                <li class="list__item">
                    <?php if($sortBy == "rating"):?>
                        <span class="list__link" data-sort="rating" data-type="<?=$orderReverse?>"><span>По</span> <span>Рейтингу</span></span>
                    <?php else:?>
                        <a class="list__link" href="#" data-sort="rating" data-type="desc"><span>По</span> <span>Рейтингу</span></a>
                    <?php endif;?>
                </li>
            </ul>
        </div>

        <?php if($allCount > 0)  {
            ?>
                <div class="catalog__count">Доступно <?= $allCount ?> <?= $countDeclension->get($allCount) ?></div>
            <?php
        } else {
            if (isset($_GET['name'])) {?>
                <div class="catalog__count catalog__count--not-found"><?=Loc::GetMessage('NOT_FOUND_REGION')?></div>
            <?php } else {?>
                <div class="catalog__count catalog__count--not-found"><?=Loc::GetMessage('NOT_FOUND')?></div>
            <?php
            }
        }?>



        <div class="catalog__list">
            <?php foreach ($arPageSections as $arSection) : ?>
                <?php
                $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="object-row" data-map-id="<?= $arSection["ID"] ?>" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
                    <div class="object-row__images">
                        <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $arSection["FULL_GALLERY"];?>]">
                            <div class="swiper-wrapper">

                                <?php $keyPhoto = 1; ?>
                                <?php $keyPhotoFullGallery = 0; ?>
                                <?php foreach ($arSection["PICTURES"] as $arPhoto) : ?>
                                    <?php if (count($arSection["PICTURES"]) > 1): ?>
                                        <?php
                                        $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " рис." . $keyPhoto;;
                                        $title = "Фото - " . $arSection["NAME"] . " рис." . $keyPhoto;
                                        ?>
                                    <?php else: ?>
                                        <?php
                                        $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"];
                                        $title = "Фото - " . $arSection["NAME"];
                                        ?>

                                    <?php endif; ?>
                                    <div class="swiper-slide" data-fullgallery-item="<?= $keyPhotoFullGallery; ?>">
                                        <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                             data-src="<?= $arPhoto["src"] ?>">
                                    </div>
                                    <?php $keyPhoto++; ?>
                                    <?php $keyPhotoFullGallery++; ?>
                                <?php endforeach; ?>

                            </div>

                            <?php if ($arSection["PICTURES"] && sizeof($arSection["PICTURES"]) > 1) : ?>
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
                            <?php endif;?>
                        </div>

                        <button class="favorite"
                                <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<?php else:?>data-favourite-add<?php endif;?>data-id="<?= $arSection["ID"] ?>">
                            <?php if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg" alt>
                            <?php else : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt>
                            <?php endif; ?>
                        </button>

                        <?php if ($arSection["IS_DISCOUNT"] == 'Y'): ?>
                            <div class="tag"><?=Loc::GetMessage('CATALOG_DISCOUNT')?></div>
                        <?php endif; ?>

                        <?php if (!empty($arSection["UF_ACTION"])) : ?>
                            <div class="tag"><?= $arSection["UF_ACTION"] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="object-row__content">
                        <div class="object-row__description">
                            <a class="object-row__title h3" onclick="setLocalStorageCatalog(event);" href="<?=$arSection["URL"]?>"><?= $arSection["NAME"] ?></a>

                            <div class="area-info">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt>
                                <div>
                                    <?php if (isset($arHLTypes[$arSection["UF_TYPE"]])) : ?><span><?= $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] ?></span><?php endif; ?>
                                    <?php if (!empty($arSection["UF_DISTANCE"])) : ?><span><?= $arSection["UF_DISTANCE"] ?></span><?php endif; ?>
                                    <?php if (!empty($arSection["UF_ADDRESS"])) : ?><span><?= $arSection["UF_ADDRESS"] ?></span><?php endif; ?>
                                    <?php if (!empty($arSection["DISCTANCE"])) : ?><span><?= $arSection["DISCTANCE"] ?> км от <?=$arSection['DISCTANCE_TO_REGION']?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="object-row__reviews">
                                <a href="<?=$arSection["URL"]?>#reviews-anchor" style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score" data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][1][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][2][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][3][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][4][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][5][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][6][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][7][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arSection["ID"]]["criterials"][8][0] ?? '0.0'?>}]">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                                    <span><?= $arReviewsAvg[$arSection["ID"]]["avg"] ?? 0  ?></span>
                                </a>
                                <a href="<?=$arSection["URL"]?>#reviews-anchor"><?= $arReviewsAvg[$arSection["ID"]]["count"] ?> <?= $reviewsDeclension->get($arReviewsAvg[$arSection["ID"]]["count"]) ?></a>
                            </div>

                            <?php if($arSection["UF_FEATURES"]):?>
                                <div class="object-row__features">
                                    <?php foreach ($arSection["UF_FEATURES"] as $featureId) :
                                        if (empty($arHLFeatures[$featureId]["UF_NAME"])) {
                                            continue;
                                        }
                                        ?>
                                        <span><?= $arHLFeatures[$featureId]["UF_NAME"] ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif;?>
                        </div>

                        <div class="object-row__order">
                            <div class="object-row__price">
                                <?php if($arSection["PRICE"] > 0):?>
                                    <div><?= number_format($arSection["PRICE"], 0, '.', ' ') ?> ₽</div>
                                    <span>Цена за одну ночь</span>
                                <?php endif;?>
                            </div>

                            <a class="button button_primary" onclick="VK.Goal('customize_product');setLocalStorageCatalog(event);" href="<?=$arSection["URL"]?>">Выбрать</a>
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
    </div>

    <div class="catalog__map <?php if(CSite::InDir('/map')): ?>catalog__on_map<?php endif; ?>" data-map-overlay>
        <div class="catalog__map-sticky">
            <div id="map"></div>

            <button class="catalog__map-fullscreen" data-map-full type="button">
                <svg class="icon icon_fullscreen" viewbox="0 0 20 20" style="width: 2rem; height: 2rem;">
                    <use xlink:href="#fullscreen" />
                </svg>
            </button>
            <?php if(CSite::InDir('/map')): ?>
                <a href="/catalog/" class="button button_primary catalog__map-halfscreen link__to_catalog">
                    <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                        <use xlink:href="#arrow-text" />
                    </svg>
                    <span>Перейти к списку</span>
                </a>
            <?php else: ?>
                <button class="button button_primary catalog__map-halfscreen" data-map-half type="button">
                    <svg class="icon icon_arrow-text" viewbox="0 0 12 8" style="width: 1.2rem; height: 0.8rem;">
                        <use xlink:href="#arrow-text" />
                    </svg>
                    <span>Перейти к списку</span>
                </button>
            <?php endif; ?>
            <div class="catalog__map-more" data-map-more-wrapper></div>
        </div>
    </div>
</div>



<?php if ($arResult['arSearchedRegions'] && is_array($arResult["SECTIONS"]) && count($arResult["SECTIONS"]) > 0) {
    ?>
    <div style="margin-top: 4.8rem;">
        <div class="slider related-projects__slider" data-slider-related>
            <div class="slider__heading">
                <div class="h3">Похожие глэмпинги рядом</div>

                <div class="slider__heading-controls">
                    <div class="swiper-button-prev">
                        <svg class="icon icon_arrow" viewbox="0 0 32 10" style="width: 3.2rem; height: 1rem;">
                            <use xlink:href="#arrow"/>
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg class="icon icon_arrow" viewbox="0 0 32 10" style="width: 3.2rem; height: 1rem;">
                            <use xlink:href="#arrow"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($arResult["SECTIONS"] as $arItem): ?>
                        <?php
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                        ?>
                        <?php if ($arItem["UF_PHOTOS"]): ?>
                            <?php $arDataFullGallery = []; ?>
                            <?php foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
                                <?php
                                $imageOriginal = CFile::GetFileArray($photoId);
                                $arDataFullGallery[] = "&quot;".$imageOriginal["SRC"]."&quot;";
                                ?>
                            <?php endforeach; ?>
                            <?php $dataFullGallery = implode(",", $arDataFullGallery); ?>
                        <?php endif; ?>
                        <div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                            <div class="object">
                                <div class="object__images">
                                    <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $dataFullGallery;?>]">
                                        <div class="swiper-wrapper">
                                            <?php if($arItem["UF_PHOTOS"]):?>
                                                <?php $keyPhoto = 1; ?>
                                                <?php foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
                                                    <?php
                                                    $arPhoto = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 360), BX_RESIZE_IMAGE_EXACT, true);
                                                    ?>
                                                    <?php if (count((array)$arItem["UF_PHOTOS"]) > 1): ?>
                                                        <?php
                                                        $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"] . " рис." . $keyPhoto;;
                                                        $title = "Фото - " . $arItem["NAME"] . " рис." . $keyPhoto;
                                                        ?>
                                                    <?php else: ?>
                                                        <?php
                                                        $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"];
                                                        $title = "Фото - " . $arItem["NAME"];
                                                        ?>
                                                    <?php endif; ?>
                                                    <div class="swiper-slide" data-fullgallery-item="<?= $keyElement; ?>">
                                                        <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= $arPhoto["src"] ?>"
                                                             alt="<?= $arItem["NAME"] ?>">
                                                    </div>
                                                    <?php $keyPhoto++; ?>
                                                <?php endforeach ?>
                                            <?php else:?>
                                                <div class="swiper-slide">
                                                    <?php
                                                    $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"];
                                                    $title = "Фото - " . $arItem["NAME"];
                                                    ?>
                                                    <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>"data-src="<?= SITE_TEMPLATE_PATH ?>/img/no_photo.png"
                                                         alt="<?= $arItem["NAME"] ?>">
                                                </div>
                                            <?php endif;?>
                                        </div>
                                        <?php if (isset($arItem["UF_PHOTOS"]) && count((array)$arItem["UF_PHOTOS"]) > 1): ?>
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

                                    <button class="favorite" data-favourite-add data-id="<?= $arItem["ID"] ?>">
                                        <?php if ($arResult["FAVOURITES"] && in_array($arItem["ID"], $arResult["FAVOURITES"])): ?>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg" alt>
                                        <?php else: ?>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt>
                                        <?php endif; ?>
                                    </button>

                                    <?php if (!empty($arItem["UF_ACTION"])): ?>
                                        <div class="tag"><?= $arItem["UF_ACTION"] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="object__heading">
                                    <a class="object__title" onclick="setLocalStorageCatalog(event);"
                                       href="<?= $arItem["URL"] ?>"><?= $arItem["NAME"] ?></a>
                                    <a href="<?=$arItem["URL"]?>#reviews-anchor" style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                                        <span><?= $arItem["RATING"] ?></span>
                                    </a>
                                </div>

                                <div class="object__marker">
                                    <div class="area-info">
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt>
                                        <div><span><?= $arItem["UF_ADDRESS"] ?></span></div>
                                    </div>

                                    <div class="object__marker-map">
                                        <a href="<?= $arItem["URL"] ?>#map">На карте</a>
                                    </div>
                                </div>
                                <?php
                                if(isset($arResult["SECTIONS_EXTERNAL"][$arItem["UF_EXTERNAL_ID"]]) && !empty($arResult["SECTIONS_EXTERNAL"][$arItem["UF_EXTERNAL_ID"]])) {
                                    $sectionPrice = $arResult["SECTIONS_EXTERNAL"][$arItem["UF_EXTERNAL_ID"]];
                                    // Если это Traveline, то делим цену на кол-во дней
                                    if($arItem["UF_EXTERNAL_SERVICE"] == 1) {
                                        $sectionPrice = round($sectionPrice / $arResult["DAYS_COUNT"]);
                                    }
                                } else {
                                    $sectionPrice = $arItem["UF_MIN_PRICE"];
                                }
                                $arItem["PRICE"] = $sectionPrice;?>

                                <a class="button button_transparent" onclick="VK.Goal('customize_product');setLocalStorageCatalog(event);"
                                   href="<?= $arItem["URL"] ?>"><?= number_format($arItem["PRICE"], 0, '.', ' ') ?>
                                    ₽</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
} ?>