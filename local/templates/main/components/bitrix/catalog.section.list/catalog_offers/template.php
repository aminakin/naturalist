<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Grid\Declension;

$reviewsDeclension = new Declension('отзыв', 'отзыва', 'отзывов');

Loc::loadMessages(__FILE__);
?>

<? if (isset($arResult["SECTIONS"]) && count($arResult["SECTIONS"]) > 0): ?>
    <div class="slider related-projects__slider" data-slider-related>
        <div class="slider__heading">
            <div class="">Похожие объекты рядом</div>
        </div>

        <div class="swiper related-projects__list">
            <div class="swiper-wrapper">
                <? foreach ($arResult["SECTIONS"] as $arItem): ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <div class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                        <div class="object-row" href="<?= $arItem["SECTION_PAGE_URL"] ?>">
                            <div class="object-row__images">
                                <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $arItem["FULL_GALLERY"]; ?>]">
                                    <div class="swiper-wrapper">
                                        <? if ($arItem["PICTURES"]): ?>
                                            <? $keyPhoto = 1; ?>
                                            <? foreach ($arItem["PICTURES"] as $keyElement => $photoId): ?>
                                                <div class="swiper-slide" data-fullgallery-item="<?= $keyElement; ?>">
                                                    <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= $photoId["src"] ?>"
                                                        alt="<?= $arItem["NAME"] ?>">
                                                </div>
                                                <? $keyPhoto++; ?>
                                            <? endforeach ?>
                                        <? else: ?>
                                            <div class="swiper-slide">
                                                <?
                                                $alt = $arResult["HL_TYPES"][$arItem["UF_TYPE"]]["UF_NAME"] . " " . $arItem["NAME"];
                                                $title = "Фото - " . $arItem["NAME"];
                                                ?>
                                                <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= SITE_TEMPLATE_PATH ?>/img/no_photo.png"
                                                    alt="<?= $arItem["NAME"] ?>">
                                            </div>
                                        <? endif; ?>
                                    </div>
                                    <? if (isset($arItem["PICTURES"]) && count((array)$arItem["PICTURES"]) > 1): ?>
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
                                    <? endif; ?>
                                </div>

                                <button class="favorite <?= ($arFavourites && in_array($arItem["ID"], $arFavourites)) ? ' active' : '' ?>" data-favourite-add data-id="<?= $arItem["ID"] ?>">
                                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z" fill="#E39250" />
                                    </svg>
                                </button>


                            </div>

                            <div class="tag_wrapper">
                                <?php if ($arItem["IS_DISCOUNT"] == 'Y'): ?>
                                    <div class="tag sale_tag">
                                        <?= $arItem["UF_SALE_LABEL"] != '' ? $arItem["UF_SALE_LABEL"] : Loc::GetMessage('CATALOG_DISCOUNT') ?> <?= $arItem["DISCOUNT_PERCENT"] ? $arItem["DISCOUNT_PERCENT"] . '%' : '' ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="object-row__content">
                                <div class="object-row__description">
                                    <a class="object-row__title" target="_blank"
                                        href="<?= $arItem["SECTION_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a>
                                    <?php
                                    if (isset($arParams['arHLTypes'][$arItem["UF_TYPE"]])) : ?>
                                        <span><?= $arParams['arHLTypes'][$arItem["UF_TYPE"]]["UF_NAME"] ?></span><?php endif; ?>

                                    <div class="object-row__reviews">
                                        <a target="_blank" href="<?= $arItem["SECTION_PAGE_URL"] ?>#reviews-anchor"
                                            style="display: flex;font-size: 1.3rem;margin-left: 0;" class="score"
                                            <? if ($arResult['arReviewsAvg'][$arItem["ID"]]["criterials"] != NULL) { ?>
                                                data-score="[{&quot;label&quot;:&quot;Удобство расположения&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][1][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Питание&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][2][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Уют&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][3][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Сервис&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][4][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Чистота&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][5][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Эстетика окружения&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][6][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Разнообразие досуга&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][7][0] ?? '0.0' ?>},{&quot;label&quot;:&quot;Соотношение цена/качество&quot;,&quot;value&quot;:<?= $arResult['arReviewsAvg'][$arItem["ID"]]["criterials"][8][0] ?? '0.0' ?>}]"
                                            <? } ?>
                                            >

                                            <img src="/local/templates/main/assets/img/star-score.svg" alt="Рейтинг">
                                            <span><?= number_format(floatval($arResult['arReviewsAvg'][$arItem["ID"]]["avg"]), 1, '.') ?? 0 ?></span>
                                        </a>
                                        <span class="dot"></span>
                                        <a target="_blank" href="<?= $arItem["SECTION_PAGE_URL"] ?>#reviews-anchor"><?= $arResult['arReviewsAvg'][$arItem["ID"]]["count"] ?? 0 ?> <?= $reviewsDeclension->get($arResult['arReviewsAvg'][$arItem["ID"]]["count"]) ?></a>
                                    </div>

                                    <div class="area-info">
                                        <img src="/local/templates/main/assets/img/location.svg" alt="Маркер">
                                        <?php if (!empty($arItem["DISCTANCE"])) : ?><span>
                                                <?= $arItem["DISCTANCE"] ?> км
                                                от <?= $arItem['DISCTANCE_TO_REGION'] ?></span><?php endif; ?>
                                    </div>

                                </div>
                                <?
                                if (isset($arResult["SECTIONS_EXTERNAL"][$arItem["UF_EXTERNAL_ID"]]) && !empty($arResult["SECTIONS_EXTERNAL"][$arItem["UF_EXTERNAL_ID"]])) {
                                    $sectionPrice = $arResult["SECTIONS_EXTERNAL"][$arItem["UF_EXTERNAL_ID"]]['PRICE'];;
                                    // Если это Traveline, то делим цену на кол-во дней
                                    if ($arItem["UF_EXTERNAL_SERVICE"] == 1) {
                                        $sectionPrice = round($sectionPrice / $arResult["DAYS_COUNT"]);
                                    }
                                } else {
                                    $sectionPrice = $arItem["UF_MIN_PRICE"];
                                }
                                $arItem["PRICE"] = $sectionPrice; ?>
                                <div class="object-row__order">
                                    <div class="object__price">
                                        <span><?= number_format((float)$arItem["PRICE"], 0, '.', ' ') ?> ₽</span>
                                        <span class="dot"></span>
                                        <span><?= Loc::getMessage('PRICE_ONE_NIGHT') ?></span>
                                    </div>
                                </div>
                            </div>

                            <a class="button button_transparent" onclick="VK.Goal('customize_product')" target="_blank"
                                href="<?= $arItem["SECTION_PAGE_URL"] ?>"><?= Loc::getMessage('FILTER_CHOOSE') ?></a>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
            <div class="slider__heading-controls">
                <div class="swiper-button-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                        <g filter="url(#filter0_b_6863_108197)">
                            <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4245 14.0495C20.6442 13.8298 21.0003 13.8298 21.22 14.0495L25.7725 18.602C25.878 18.7075 25.9373 18.8506 25.9373 18.9998C25.9373 19.149 25.878 19.292 25.7725 19.3975L21.22 23.95C21.0003 24.1697 20.6442 24.1697 20.4245 23.95C20.2048 23.7303 20.2048 23.3742 20.4245 23.1545L24.5793 18.9998L20.4245 14.845C20.2048 14.6253 20.2048 14.2692 20.4245 14.0495Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0625 19C12.0625 18.6893 12.3143 18.4375 12.625 18.4375H25.2475C25.5582 18.4375 25.81 18.6893 25.81 19C25.81 19.3107 25.5582 19.5625 25.2475 19.5625H12.625C12.3143 19.5625 12.0625 19.3107 12.0625 19Z" fill="white" />
                        </g>
                        <defs>
                            <filter id="filter0_b_6863_108197" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_6863_108197" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_6863_108197" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                        <g filter="url(#filter0_b_6863_108197)">
                            <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4245 14.0495C20.6442 13.8298 21.0003 13.8298 21.22 14.0495L25.7725 18.602C25.878 18.7075 25.9373 18.8506 25.9373 18.9998C25.9373 19.149 25.878 19.292 25.7725 19.3975L21.22 23.95C21.0003 24.1697 20.6442 24.1697 20.4245 23.95C20.2048 23.7303 20.2048 23.3742 20.4245 23.1545L24.5793 18.9998L20.4245 14.845C20.2048 14.6253 20.2048 14.2692 20.4245 14.0495Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0625 19C12.0625 18.6893 12.3143 18.4375 12.625 18.4375H25.2475C25.5582 18.4375 25.81 18.6893 25.81 19C25.81 19.3107 25.5582 19.5625 25.2475 19.5625H12.625C12.3143 19.5625 12.0625 19.3107 12.0625 19Z" fill="white" />
                        </g>
                        <defs>
                            <filter id="filter0_b_6863_108197" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_6863_108197" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_6863_108197" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </div>
<? endif; ?>