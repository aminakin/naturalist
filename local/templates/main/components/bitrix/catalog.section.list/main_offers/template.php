<?

use Naturalist\Reviews;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $arFavourites;

$allCount = count($arResult["SECTIONS"]);
$page = $_REQUEST['page'] ?? 1;
$pageCount = ceil($allCount / $arParams["ITEMS_COUNT"]);
if ($pageCount > 1) {
    $arResult["SECTIONS"] = array_slice($arResult["SECTIONS"], ($page - 1) * $arParams["ITEMS_COUNT"],
        $arParams["ITEMS_COUNT"]);
}

// Отзывы
$arCampingIDs = array_map(function ($a) {
    return $a["ID"];
}, $arResult["SECTIONS"]);
$arReviewsAvg = Reviews::getCampingRating($arCampingIDs);
?>

<div class="objects" data-offers-container>
    <div class="objects__heading" data-tab-mobile>
        <button class="objects__heading-control h1" data-tab-mobile-control type="button">Все предложения</button>
        <ul class="list">
            <? foreach ($arParams["TABS"] as $code => $tab): ?>
                <li class="list__item <?= ($code == "all" ? "list__item_active" : "");?>"><a class="list__link" data-offers-tab-switch="<?= $tab['CODE']; ?>"><?= $tab['NAME']; ?></a></li>
            <? endforeach; ?>
        </ul>
    </div>

    <div class="objects__list">
        <? if (!empty($arResult["SECTIONS"])): ?>
            <? foreach ($arResult["SECTIONS"] as $arItem): ?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                    array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <? if ($arItem["UF_PHOTOS"]): ?>
                    <? $arDataFullGallery = []; ?>
                    <? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
                        <?
                        $imageOriginal = CFile::GetFileArray($photoId);
                        $arDataFullGallery[] = "&quot;" . $imageOriginal["SRC"] . "&quot;";
                        ?>
                    <? endforeach; ?>
                    <? $dataFullGallery = implode(",", $arDataFullGallery); ?>
                <? endif; ?>

                <div class="object" data-id="<?= $arItem["ID"] ?>" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                    <div class="object__images">
                        <div class="swiper slider-gallery" data-slider-object="data-slider-object"
                             data-fullgallery="[<?= $dataFullGallery; ?>]">
                            <div class="swiper-wrapper">
                                <? if ($arItem["UF_PHOTOS"]): ?>
                                    <? $keyPhoto = 1; ?>
                                    <? foreach ($arItem["UF_PHOTOS"] as $keyElement => $photoId): ?>
                                        <?
                                        $arPhoto = CFile::ResizeImageGet($photoId,
                                            array('width' => 600, 'height' => 360), BX_RESIZE_IMAGE_EXACT, true);
                                        ?>
                                        <? if (count($arItem["UF_PHOTOS"]) > 1): ?>
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
                                            <img class="" loading="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                                 src="<?= $arPhoto["src"] ?>"
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
                                        <img class="" loading="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                             src="<?= SITE_TEMPLATE_PATH ?>/img/no_photo.png"
                                             alt="<?= $arItem["NAME"] ?>">
                                    </div>
                                <? endif; ?>
                            </div>

                            <? if (isset($arItem["UF_PHOTOS"]) && !empty($arItem["UF_PHOTOS"]) && count($arItem["UF_PHOTOS"]) > 1): ?>
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
                                <? if ($arFavourites && in_array($arItem["ID"],
                                    $arFavourites)) : ?>data-favourite-remove<? else: ?>data-favourite-add<? endif; ?>
                                data-id="<?= $arItem["ID"] ?>">
                            <? if ($arFavourites && in_array($arItem["ID"], $arFavourites)) : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite-active.svg"
                                     alt="Добавлен в избранное">
                            <? else : ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt="Добавить в избранное">
                            <? endif; ?>
                        </button>

                        <? if ($arItem["IS_DISCOUNT"] == 'Y'): ?>
                            <div class="tag"><?=Loc::GetMessage('CATALOG_DISCOUNT')?></div>
                        <? endif; ?>

                        <? if (!empty($arItem["UF_ACTION"])): ?>
                            <div class="tag"><?= $arItem["UF_ACTION"] ?></div>
                        <? endif; ?>
                    </div>

                    <div class="object__heading">
                        <a class="object__title" href="<?= $arItem["SECTION_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a>
                        <a href="<?=$arItem["SECTION_PAGE_URL"]?>#reviews-anchor" style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score" data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][1][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][2][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][3][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][4][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][5][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][6][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][7][0] ?? '0.0'?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arReviewsAvg[$arItem["ID"]]["criterials"][8][0] ?? '0.0'?>}]">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg"
                                 alt="Рейтинг"><span><?= $arReviewsAvg[$arItem["ID"]]["avg"] ?? 0 ?></span>
                        </a>
                    </div>

                    <div class="object__marker">
                        <div class="area-info">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt="Маркер">
                            <div><span><?= $arItem["UF_ADDRESS"] ?></span></div>
                        </div>

                        <div class="object__marker-map">
                            <a href="<?= $arItem["SECTION_PAGE_URL"] ?>#map">На карте</a>
                        </div>
                    </div>

                    <a class="button button_transparent" onclick="VK.Goal('customize_product')"
                       href="<?= $arItem["SECTION_PAGE_URL"] ?>"><?= number_format($arItem["UF_MIN_PRICE"], 0, '.',
                            ' ') ?>
                        ₽</a>
                </div>
            <? endforeach; ?>
        <? else: ?>
            <span class="news-preview__title">Нет предложений</span>
        <? endif; ?>
    </div>
    <?if ($arParams['SHOW_MORE_LINK']):?>
        <div class="objects__more">
            <a href="<?=$arParams['SHOW_MORE_LINK']?>">Показать ещё</a>
        </div>
    <?endif;?>
</div>