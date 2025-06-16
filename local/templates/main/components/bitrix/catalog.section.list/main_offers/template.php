<?

use Naturalist\Reviews;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Grid\Declension;

Loc::loadMessages(__FILE__);

global $arFavourites;

$allCount = count($arResult["SECTIONS"]);
$page = $_REQUEST['page'] ?? 1;
$pageCount = ceil($allCount / $arParams["ITEMS_COUNT"]);
if ($pageCount > 1) {
    $arResult["SECTIONS"] = array_slice(
        $arResult["SECTIONS"],
        ($page - 1) * $arParams["ITEMS_COUNT"],
        $arParams["ITEMS_COUNT"]
    );
}

// Отзывы
$arCampingIDs = array_map(function ($a) {
    return $a["ID"];
}, $arResult["SECTIONS"]);

$arReviewsAvg = Reviews::getCampingRating($arCampingIDs);

$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');
?>
<div class="objects" data-offers-container>
    <?/*div class="objects__heading" data-tab-mobile>
        <button class="objects__heading-control h1" data-tab-mobile-control type="button">Все предложения</button>
        <ul class="list">
            <? foreach ($arParams["TABS"] as $code => $tab) : ?>
                <li class="list__item <?= ($code == "all" ? "list__item_active" : ""); ?>"><a class="list__link" data-offers-tab-switch="<?= $tab['CODE']; ?>"><?= $tab['NAME']; ?></a></li>
            <? endforeach; ?>
        </ul>
    </div*/ ?>

    <div class="objects__list">
        <? if (!empty($arResult["SECTIONS"])) : ?>
            <? foreach ($arResult["SECTIONS"] as $arItem) : ?>
                <?
                $this->AddEditAction(
                    $arItem['ID'],
                    $arItem['EDIT_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
                );
                $this->AddDeleteAction(
                    $arItem['ID'],
                    $arItem['DELETE_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                    array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
                );
                ?>
                <? /*if ($arItem["UF_PHOTOS"]) : ?>
                    <? $arDataFullGallery = []; ?>
                    <? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId) : ?>
                        <?
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "&quot;" . $imageOriginal["SRC"] . "&quot;";
                        ?>
                    <? endforeach; ?>
                    <? $dataFullGallery = implode(",", $arDataFullGallery); ?>
                <? endif; */ ?>

                <div class="object" href="<?= $arItem["SECTION_PAGE_URL"] ?>" data-id="<?= $arItem["ID"] ?>" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                    <div class="object__images">
                        <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $arItem["FULL_GALLERY"]; ?>]">
                            <div class="swiper-wrapper">
                                <?

                                if ($arItem["PICTURES"]) : ?>
                                    <? $keyPhoto = 1; ?>
                                    <? foreach ($arItem["PICTURES"] as $keyElement => $photoId) : ?>
                                        <? //var_export($photoId);
                                        /*$arPhoto = CFile::ResizeImageGet(
                                            $photoId,
                                            array('width' => 600, 'height' => 400),
                                            BX_RESIZE_IMAGE_EXACT,
                                            true
                                        );*/
                                        ?>
                                        <? /*if (count($arItem["UF_PHOTOS"]) > 1) : ?>
                                            <?
                                            $alt = $arResult["HL_TYPES"][$arItem["UF_TYPE"]]["UF_NAME"] . " " . $arItem["NAME"] . " рис." . $keyPhoto;;
                                            $title = "Фото - " . $arItem["NAME"] . " рис." . $keyPhoto;
                                            ?>
                                        <? else : ?>
                                            <?
                                            $alt = $arResult["HL_TYPES"][$arItem["UF_TYPE"]]["UF_NAME"] . " " . $arItem["NAME"];
                                            $title = "Фото - " . $arItem["NAME"];
                                            ?>
                                        <? endif; */ ?>
                                        <div class="swiper-slide" data-fullgallery-item="<?= $keyElement; ?>">
                                            <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= $photoId["src"] ?>" alt="<?= $arItem["NAME"] ?>">
                                        </div>
                                        <? $keyPhoto++; ?>
                                    <? endforeach ?>
                                <? else : ?>
                                    <?
                                    $alt = $arResult["HL_TYPES"][$arItem["UF_TYPE"]]["UF_NAME"] . " " . $arItem["NAME"];
                                    $title = "Фото - " . $arItem["NAME"];
                                    ?>
                                    <div class="swiper-slide">
                                        <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= SITE_TEMPLATE_PATH ?>/img/no_photo.png" alt="<?= $arItem["NAME"] ?>">
                                    </div>
                                <? endif; ?>
                            </div>

                            <? if (isset($arItem["PICTURES"]) && !empty($arItem["PICTURES"]) && count($arItem["PICTURES"]) > 1) : ?>
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
                                <div class="swiper-pagination-wrapper">
                                    <div class="swiper-pagination"></div>
                                </div>
                            <? endif; ?>
                        </div>

                        <button class="favorite<?= ($arFavourites && in_array($arItem["ID"], $arFavourites)) ? ' active' : '' ?>" <? if ($arFavourites && in_array(
                                                                                                                                        $arItem["ID"],
                                                                                                                                        $arFavourites
                                                                                                                                    )) : ?>data-favourite-remove<? else : ?>data-favourite-add<? endif; ?> data-id="<?= $arItem["ID"] ?>">
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z" fill="#E39250" />
                            </svg>
                        </button>
                    </div>
                    <? if ($arItem["IS_DISCOUNT"] == 'Y') : ?>
                        <div class="tag"><?= $arItem["UF_SALE_LABEL"] != '' ? $arItem["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?></div>
                    <? endif; ?>

                    <? if (!empty($arItem["UF_ACTION"])) : ?>
                        <div class="tag"><?= $arItem["UF_ACTION"] ?></div>
                    <? endif; ?>

                    <div class="object__info">
                        <div class="object__heading">
                            <a target="_blank" rel="noopener" class="object__title" href="<?= $arItem["SECTION_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a>
                            <a target="_blank" href="<?= $arItem["SECTION_PAGE_URL"] ?>#reviews-anchor" class="score"
                               <? if ($arReviewsAvg[$arItem["ID"]]["criterials"] != NULL) { ?>
                                   data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][1][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][2][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][3][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][4][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][5][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][6][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][7][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][8][0] ?? '0.0' ?>}]"
                               <? } ?>
                               >
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="Рейтинг">
                                <?
                                    $yandexReviewsCount = $arItem['yandexReviews']['count'];
                                    $allReviews = $yandexReviewsCount + $arReviewsAvg[$arItem["ID"]]["count"];
                                    $ratingAvg = $arReviewsAvg[$arItem["ID"]]["avg"] ?? $arItem['yandexReviews']['avg'];
                                ?>
                                <? if ($allReviews > 0) { ?>
                                    <span><?=$ratingAvg?></span>
                                    <span class="dot"></span>
                                    <?= $arReviewsAvg[$arItem["ID"]]["count"] + $yandexReviewsCount ?? 0 ?>
                                    <?= $reviewsDeclension->get($arReviewsAvg[$arItem["ID"]]["count"]) ?>
                                <? } else { ?>
                                    <?= Loc::getMessage('NO_REVIEW') ?>
                                <? } ?>
                            </a>
                        </div>

                        <div class="object__marker">
                            <div class="area-info">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/location.svg" alt="Маркер">
                                <div><span><?= $arItem["UF_ADDRESS"] ?></span></div>
                            </div>

                            <?/*div class="object__marker-map">
                                <a href="<?= $arItem["SECTION_PAGE_URL"] ?>#map">На карте</a>
                            </div*/ ?>
                        </div>
                        <div class="object__price">
                            <span><?= number_format((float)$arItem["UF_MIN_PRICE"], 0, '.', ' ') ?> ₽</span>
                            <span class="dot"></span>
                            <span><?= Loc::getMessage('PRICE_ONE_NIGHT') ?></span>
                        </div>
                    </div>
                    <a class="button button_transparent" target="_blank" onclick="VK.Goal('customize_product')" href="<?= $arItem["SECTION_PAGE_URL"] ?>"><?= Loc::getMessage('FILTER_CHOOSE'); ?></a>
                </div>
            <? endforeach; ?>
        <? else : ?>
            <span class="news-preview__title">Нет предложений</span>
        <? endif; ?>
    </div>
    <? if ($arParams['SHOW_MORE_LINK']) : ?>
        <div class="objects__more">
            <a href="<?= $arParams['SHOW_MORE_LINK'] ?>">Показать ещё</a>
        </div>
    <? endif; ?>
</div>