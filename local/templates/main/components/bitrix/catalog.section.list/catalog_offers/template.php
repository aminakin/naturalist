<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<? if (isset($arResult["SECTIONS"]) && count($arResult["SECTIONS"]) > 0): ?>
    <div class="related-projects__mobile">
        <div class="h3">Похожие глэмпинги рядом</div>
        <div class="objects">
            <? foreach ($arResult["SECTIONS"] as $arItem): ?>
				<? if ($arItem["UF_PHOTOS"]): ?>
					<? $arDataFullGallery = []; ?>
					<? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
						<?
						$imageOriginal = CFile::GetFileArray($photoId);
						$arDataFullGallery[] = "&quot;".$imageOriginal["SRC"]."&quot;";
						?>
					<? endforeach; ?>
					<? $dataFullGallery = implode(",", $arDataFullGallery); ?>
				<? endif; ?>
                <div class="object">
                    <div class="object__images">
                        <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $dataFullGallery;?>]">
                            <div class="swiper-wrapper">
                                <? if ($arItem["UF_PHOTOS"]): ?>
                                    <? $keyPhoto = 1; ?>
                                    <? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
                                        <?
                                        $arPhoto = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 360), BX_RESIZE_IMAGE_EXACT, true);
                                        ?>
                                        <? if (count((array)$arItem["UF_PHOTOS"]) > 1): ?>
                                            <?
                                            $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"] . " рис." . $keyPhoto;;
                                            $title = "Фото - " . $arItem["NAME"] . " рис." . $keyPhoto;
                                            ?>
                                        <? else: ?>
                                            <?
                                            $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"];
                                            $title = "Фото - " . $arItem["NAME"];
                                            ?>
                                        <? endif; ?>
                                        <div class="swiper-slide" data-fullgallery-item="<?= $keyElement; ?>">
                                            <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= $arPhoto["src"] ?>"
                                                 alt="<?= $arItem["NAME"] ?>">
                                        </div>
                                        <? $keyPhoto++; ?>
                                    <? endforeach ?>
                                <? else: ?>
                                    <?
                                    $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"];
                                    $title = "Фото - " . $arItem["NAME"];
                                    ?>
                                    <div class="swiper-slide">
                                        <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= SITE_TEMPLATE_PATH ?>/img/no_photo.png"
                                             alt="<?= $arItem["NAME"] ?>">
                                    </div>
                                <? endif; ?>
                            </div>

                            <? if (isset($arItem["UF_PHOTOS"]) && count((array)$arItem["UF_PHOTOS"]) > 1): ?>
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
                            <? endif; ?>
                        </div>

                        <button class="favorite"
                                <? if ($arResult["FAVOURITES"] && in_array($arItem["ID"], $arResult["FAVOURITES"])) : ?>data-favourite-remove<? else: ?>data-favourite-add<? endif; ?>
                                data-id="<?= $arItem["ID"] ?>">
                            <? if ($arResult["FAVOURITES"] && in_array($arItem["ID"], $arResult["FAVOURITES"])) : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg" alt>
                            <? else : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt>
                            <? endif; ?>
                        </button>
                    </div>

                    <div class="object__heading">
                        <a class="object__title" href="<?= $arItem["URL"] ?>"><?= $arItem["NAME"] ?></a>
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

                    <a class="button button_transparent" onclick="VK.Goal('customize_product')"
                       href="<?= $arItem["URL"] ?>"><?= number_format($arItem["UF_MIN_PRICE"], 0, '.', ' ') ?>
                        ₽</a>
                </div>
            <? endforeach; ?>
        </div>
    </div>

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
                <? foreach ($arResult["SECTIONS"] as $arItem): ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
					<? if ($arItem["UF_PHOTOS"]): ?>
						<? $arDataFullGallery = []; ?>
						<? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
							<?
							$imageOriginal = CFile::GetFileArray($photoId);
							$arDataFullGallery[] = "&quot;".$imageOriginal["SRC"]."&quot;";
							?>
						<? endforeach; ?>
						<? $dataFullGallery = implode(",", $arDataFullGallery); ?>
					<? endif; ?>
                    <div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                        <div class="object">
                            <div class="object__images">
                                <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $dataFullGallery;?>]">
                                    <div class="swiper-wrapper">
                                        <?if($arItem["UF_PHOTOS"]):?>
                                            <? $keyPhoto = 1; ?>
                                            <? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
                                                <?
                                                $arPhoto = CFile::ResizeImageGet($photoId, array('width' => 600, 'height' => 360), BX_RESIZE_IMAGE_EXACT, true);
                                                ?>
                                                <? if (count((array)$arItem["UF_PHOTOS"]) > 1): ?>
                                                    <?
                                                    $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"] . " рис." . $keyPhoto;;
                                                    $title = "Фото - " . $arItem["NAME"] . " рис." . $keyPhoto;
                                                    ?>
                                                <? else: ?>
                                                    <?
                                                    $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"];
                                                    $title = "Фото - " . $arItem["NAME"];
                                                    ?>
                                                <? endif; ?>
                                                <div class="swiper-slide" data-fullgallery-item="<?= $keyElement; ?>">
                                                    <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= $arPhoto["src"] ?>"
                                                         alt="<?= $arItem["NAME"] ?>">
                                                </div>
                                                <? $keyPhoto++; ?>
                                            <? endforeach ?>
                                        <?else:?>
                                            <div class="swiper-slide">
                                                <?
                                                $alt = $arResult["HL_TYPES"][$arItem["ID"]]["UF_NAME"] . " " . $arItem["NAME"];
                                                $title = "Фото - " . $arItem["NAME"];
                                                ?>
                                                <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>"data-src="<?= SITE_TEMPLATE_PATH ?>/img/no_photo.png"
                                                     alt="<?= $arItem["NAME"] ?>">
                                            </div>
                                        <?endif;?>
                                    </div>
                                    <? if (isset($arItem["UF_PHOTOS"]) && count((array)$arItem["UF_PHOTOS"]) > 1): ?>
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
                                    <? endif; ?>
                                </div>

                                <button class="favorite" data-favourite-add data-id="<?= $arItem["ID"] ?>">
                                    <? if ($arResult["FAVOURITES"] && in_array($arItem["ID"], $arResult["FAVOURITES"])): ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg" alt>
                                    <? else: ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt>
                                    <? endif; ?>
                                </button>

                                <? if (!empty($arItem["UF_ACTION"])): ?>
                                    <div class="tag"><?= $arItem["UF_ACTION"] ?></div>
                                <? endif; ?>
                            </div>

                            <div class="object__heading">
                                <a class="object__title"
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
                            <?
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

                            <a class="button button_transparent" onclick="VK.Goal('customize_product')"
                               href="<?= $arItem["URL"] ?>"><?= number_format($arItem["PRICE"], 0, '.', ' ') ?>
                                ₽</a>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>
<? endif; ?>