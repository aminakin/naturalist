<?php
/** @var  $arElements */
/** @var  $arExternalInfo */

use Naturalist\Users;

foreach ($arElements as $arElement):
    $arElementsTariffs[$arElement['ID']] = $arElement;
endforeach;
$arParentView = [];

foreach ($arExternalInfo as $idNumber => $arTariffs):
    ?>
    <?php foreach ($arTariffs as $keyTariff => $arTariff):
    if (empty($arTariff['prices']) || empty($arElementsTariffs[$idNumber])) {
        continue;
    }

        $arElement = $arElementsTariffs[$idNumber];

        if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] != 0 && !in_array($arElement["PROPERTY_PARENT_ID_VALUE"] . '-' . $arTariff['tariffId'], $arParentView)) {
            $arParentView[] = $arElement["PROPERTY_PARENT_ID_VALUE"] . '-' . $arTariff['tariffId'];
        } elseif ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] == 0 && !in_array($arElement["PROPERTY_EXTERNAL_ID_VALUE"] . '-' . $arTariff['tariffId'], $arParentView)) {
            $arParentView[] = $arElement["PROPERTY_EXTERNAL_ID_VALUE"] . '-' . $arTariff['tariffId'];
        } else {
            continue;
        }

        if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0 && !empty($arElementsParent[$arElement['PROPERTY_PARENT_ID_VALUE']])) {
            $arElement = $arElementsParent[$arElement['PROPERTY_PARENT_ID_VALUE']];
            $arElement["ID"] = $arElementsTariffs[$idNumber]["ID"];
            $arElement["PROPERTY_EXTERNAL_ID_VALUE"] = $arElementsTariffs[$idNumber]["PROPERTY_EXTERNAL_ID_VALUE"];
        }

    ?>
        <div class="room">
            <div class="room__top">
                <? if ($arElement["PICTURES"]): ?>
                    <div class="room__images">
                        <? if ($arElement['PROPERTY_ROOMTOUR_VALUE']) { ?>
                            <a class="room__tour" href="<?= CFile::GetPath($arElement['PROPERTY_ROOMTOUR_VALUE']) ?>" data-fancybox="gallery_<?= $arElement['ID'] ?>" data-caption="<?= $arElement["NAME"] ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M11.0471 1.5H6.95215V4.77H11.0471V1.5Z" fill="black" />
                                    <path d="M12.1729 1.5V4.77H16.4029C16.0204 2.7075 14.4979 1.5075 12.1729 1.5Z" fill="black" />
                                    <path d="M1.5 5.89502V12.1425C1.5 14.8725 3.1275 16.5 5.8575 16.5H12.1425C14.8725 16.5 16.5 14.8725 16.5 12.1425V5.89502H1.5ZM10.83 12.135L9.27 13.035C8.94 13.2225 8.6175 13.32 8.3175 13.32C8.0925 13.32 7.89 13.2675 7.7025 13.1625C7.2675 12.915 7.0275 12.405 7.0275 11.745V9.94502C7.0275 9.28502 7.2675 8.77502 7.7025 8.52752C8.1375 8.27252 8.6925 8.31752 9.27 8.65502L10.83 9.55502C11.4075 9.88502 11.7225 10.35 11.7225 10.8525C11.7225 11.355 11.4 11.7975 10.83 12.135Z" fill="black" />
                                    <path d="M5.82766 1.5C3.50266 1.5075 1.98016 2.7075 1.59766 4.77H5.82766V1.5Z" fill="black" />
                                </svg>
                                Румтур
                            </a>
                        <? } ?>
                        <div class="swiper slider-gallery" data-slider-object="data-slider-object">
                            <div class="swiper-wrapper">
                                <? $keyPhoto = 1; ?>
                                <? foreach ($arElement["PICTURES"] as $arPhoto): ?>
                                    <? if (count($arElement["PICTURES"]) > 1): ?>
                                        <?
                                        $alt = $arResult["arSection"]["NAME"] . " " . $arElement["NAME"] . " рис." . $keyPhoto;;
                                        $title = "Фото - " . $arElement["NAME"] . " рис." . $keyPhoto;
                                        ?>
                                    <? else: ?>
                                        <?
                                        $alt = $arResult["arSection"]["NAME"] . " " . $arElement["NAME"];
                                        $title = "Фото - " . $arElement["NAME"];
                                        ?>
                                    <? endif; ?>
                                    <div class="swiper-slide">
                                        <a href="<?= $arPhoto["big"] ?>" data-fancybox="gallery_<?= $arElement['ID'] ?>" data-caption="<?= $arElement["NAME"] ?>">
                                            <img class="" loading="lazy" alt="<?= $alt; ?>" title="<?= $title; ?>" src="<?= $arPhoto["src"] ?>">
                                        </a>
                                    </div>
                                    <? $keyPhoto++; ?>
                                <? endforeach; ?>
                            </div>

                            <? if (count($arElement["PICTURES"]) > 1): ?>
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
                    </div>
                    <script>
                        Fancybox.bind(' [data-fancybox="gallery_<?= $arElement['ID'] ?>" ]', {
                            Toolbar: {
                                display: {
                                    left: ["infobar"],
                                    middle: [],
                                    right: ["close"],
                                },
                            },

                            commonCaption: true,

                            Thumbs: {
                                type: "classic",
                            },
                        });
                    </script>
                <? endif; ?>

                <div class="room__content">
                    <div class="room__description">
                        <div class="room__description-title"><?= $arElement["NAME"] . ' ' . ($arTariff['value']['PROPERTY_NAME_DETAIL_VALUE'] ?? $arTariff['value']['NAME']) ?></div>
                        <? if (!empty($arElement["PROPERTY_SQUARE_VALUE"])): ?>
                            <div class="room__features">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M10.0001 18.9583C9.95013 18.9583 9.90013 18.95 9.85846 18.9417C9.80846 18.9333 9.75846 18.9083 9.70846 18.8833L7.8668 17.9667C7.55847 17.8083 7.43346 17.4333 7.58346 17.125C7.7418 16.8167 8.1168 16.6917 8.42513 16.8417L9.37513 17.3167V16.0167C9.37513 15.675 9.65846 15.3917 10.0001 15.3917C10.3418 15.3917 10.6251 15.675 10.6251 16.0167V17.3167L11.5751 16.8417C11.8835 16.6917 12.2585 16.8167 12.4168 17.125C12.5751 17.4333 12.4501 17.8083 12.1335 17.9667L10.2918 18.8833C10.2418 18.9083 10.1918 18.925 10.1418 18.9417C10.0918 18.95 10.0501 18.9583 10.0001 18.9583ZM15.5585 16.1833C15.3335 16.1833 15.1085 16.0583 15.0001 15.8417C14.8418 15.5333 14.9668 15.1583 15.2835 15L16.7918 14.25V12.325C16.7918 11.9833 17.0751 11.7 17.4168 11.7C17.7585 11.7 18.0418 11.9833 18.0418 12.325V14.6417C18.0418 14.875 17.9085 15.0917 17.7001 15.2L15.8501 16.125C15.7418 16.1583 15.6501 16.1833 15.5585 16.1833ZM4.4418 16.1833C4.35013 16.1833 4.25013 16.1583 4.1668 16.1167L2.3168 15.1917C2.10846 15.0833 1.97513 14.8667 1.97513 14.6333V12.3167C1.97513 11.975 2.25847 11.6917 2.60013 11.6917C2.9418 11.6917 3.22513 11.975 3.22513 12.3167V14.2417L4.73346 14.9917C5.0418 15.15 5.16679 15.525 5.01679 15.8333C4.89179 16.05 4.67513 16.1833 4.4418 16.1833ZM10.0001 12.0167C9.65846 12.0167 9.37513 11.7333 9.37513 11.3917V9.46666L7.8668 8.71667C7.55847 8.55833 7.43346 8.18332 7.58346 7.87499C7.7418 7.56666 8.1168 7.44166 8.42513 7.59166L10.0001 8.375L11.5751 7.59166C11.8835 7.44166 12.2585 7.55832 12.4168 7.87499C12.5751 8.19166 12.4501 8.55833 12.1335 8.71667L10.6251 9.46666V11.3917C10.6251 11.7333 10.3418 12.0167 10.0001 12.0167ZM17.4085 8.30833C17.0668 8.30833 16.7835 8.02499 16.7835 7.68333V6.38333L15.8335 6.85832C15.5251 7.01666 15.1501 6.89166 14.9918 6.57499C14.8335 6.26666 14.9585 5.89166 15.2751 5.73333L16.0085 5.36666L15.2751 4.99999C14.9668 4.84166 14.8418 4.46666 14.9918 4.15833C15.1501 3.85 15.5251 3.72499 15.8335 3.87499L17.6751 4.79166C17.6918 4.79999 17.7168 4.80832 17.7335 4.82499C17.7835 4.84999 17.8251 4.89166 17.8668 4.93333C17.8918 4.96666 17.9168 4.99999 17.9418 5.03332C17.9751 5.09165 18.0001 5.14999 18.0168 5.21666C18.0251 5.26666 18.0335 5.31665 18.0335 5.35832V5.36666V7.66666C18.0335 8.03332 17.7501 8.30833 17.4085 8.30833ZM2.5918 8.30833C2.25013 8.30833 1.9668 8.02499 1.9668 7.68333V5.38333V5.37499C1.9668 5.32499 1.97513 5.27499 1.98346 5.23333C2.00013 5.16666 2.02513 5.10833 2.05846 5.04999C2.08346 5.00832 2.10846 4.97499 2.1418 4.94165C2.17513 4.90832 2.2168 4.875 2.25846 4.85C2.27513 4.84167 2.30013 4.82499 2.3168 4.81666L4.15846 3.89999C4.46679 3.74999 4.8418 3.86666 5.00013 4.18333C5.15846 4.5 5.03347 4.86666 4.7168 5.02499L3.98346 5.39166L4.7168 5.75833C5.02513 5.91666 5.15013 6.29166 5.00013 6.59999C4.8418 6.90833 4.47513 7.03332 4.15846 6.88332L3.20846 6.40833V7.70833C3.2168 8.03333 2.9418 8.30833 2.5918 8.30833ZM11.8501 3.21666C11.7585 3.21666 11.6585 3.19166 11.5751 3.14999L10.0001 2.36666L8.42513 3.14999C8.1168 3.30833 7.7418 3.18333 7.58346 2.86666C7.42513 2.55833 7.55013 2.18332 7.8668 2.02499L9.7168 1.1C9.8918 1.00833 10.1001 1.00833 10.2751 1.1L12.1251 2.02499C12.4335 2.18332 12.5585 2.55833 12.4085 2.86666C12.3001 3.09166 12.0835 3.21666 11.8501 3.21666Z" fill="black" />
                                </svg>
                                <?= $arElement["PROPERTY_SQUARE_VALUE"] ?> м²
                            </div>
                        <? endif; ?>
                        <? if (!empty($arElement["PROPERTY_ROOMS_VALUE"])): ?>
                            <div class="room__features">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.95203 1.4585H10.0473C11.8729 1.45848 13.3068 1.45848 14.4263 1.60899C15.5735 1.76323 16.4837 2.08576 17.1989 2.80092C17.9141 3.51609 18.2366 4.4263 18.3908 5.57352C18.5414 6.69303 18.5414 8.12693 18.5413 9.95252V10.0478C18.5414 11.8734 18.5414 13.3073 18.3908 14.4268C18.2366 15.574 17.9141 16.4842 17.1989 17.1994C16.4837 17.9146 15.5735 18.2371 14.4263 18.3913C13.3068 18.5418 11.8729 18.5418 10.0473 18.5418H9.95204C8.12644 18.5418 6.69254 18.5418 5.57303 18.3913C4.42581 18.2371 3.5156 17.9146 2.80044 17.1994C2.08527 16.4842 1.76274 15.574 1.6085 14.4268C1.45799 13.3073 1.458 11.8734 1.45801 10.0478V9.95252C1.458 8.12693 1.45799 6.69303 1.6085 5.57352C1.76274 4.4263 2.08527 3.51609 2.80044 2.80092C3.5156 2.08576 4.42581 1.76323 5.57303 1.60899C6.69254 1.45848 8.12644 1.45848 9.95203 1.4585ZM5.73959 2.84784C4.73098 2.98345 4.12852 3.2406 3.68432 3.68481C3.24012 4.12901 2.98296 4.73147 2.84736 5.74008C2.70934 6.76666 2.70801 8.11652 2.70801 10.0002C2.70801 11.8838 2.70934 13.2337 2.84736 14.2602C2.98296 15.2689 3.24012 15.8713 3.68432 16.3155C4.12852 16.7597 4.73098 17.0169 5.73959 17.1525C6.76617 17.2905 8.11603 17.2918 9.99967 17.2918C11.8833 17.2918 13.2332 17.2905 14.2598 17.1525C15.2684 17.0169 15.8708 16.7597 16.315 16.3155C16.7592 15.8713 17.0164 15.2689 17.152 14.2602C17.29 13.2337 17.2913 11.8838 17.2913 10.0002C17.2913 8.11652 17.29 6.76666 17.152 5.74008C17.0164 4.73147 16.7592 4.12901 16.315 3.68481C15.8708 3.2406 15.2684 2.98345 14.2598 2.84784C13.2332 2.70982 11.8833 2.7085 9.99967 2.7085C8.11603 2.7085 6.76617 2.70982 5.73959 2.84784Z" fill="black" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99967 1.4585C10.3449 1.4585 10.6247 1.73832 10.6247 2.0835V3.66683C10.6247 4.01201 10.3449 4.29183 9.99967 4.29183C9.6545 4.29183 9.37467 4.01201 9.37467 3.66683V2.0835C9.37467 1.73832 9.6545 1.4585 9.99967 1.4585ZM9.99967 7.00015C10.3449 7.00015 10.6247 7.27998 10.6247 7.62515V9.37515H12.3747C12.7199 9.37515 12.9997 9.65498 12.9997 10.0002C12.9997 10.3453 12.7199 10.6252 12.3747 10.6252H10.6247V12.3752C10.6247 12.7203 10.3449 13.0002 9.99967 13.0002C9.6545 13.0002 9.37467 12.7203 9.37467 12.3752V10.6252H7.62467C7.2795 10.6252 6.99967 10.3453 6.99967 10.0002C6.99967 9.65498 7.2795 9.37515 7.62467 9.37515H9.37467V7.62515C9.37467 7.27998 9.6545 7.00015 9.99967 7.00015ZM1.45801 10.0002C1.45801 9.65498 1.73783 9.37516 2.08301 9.37516H3.66634C4.01152 9.37516 4.29134 9.65498 4.29134 10.0002C4.29134 10.3453 4.01152 10.6252 3.66634 10.6252H2.08301C1.73783 10.6252 1.45801 10.3453 1.45801 10.0002ZM15.708 10.0002C15.708 9.65498 15.9878 9.37516 16.333 9.37516H17.9163C18.2615 9.37516 18.5413 9.65498 18.5413 10.0002C18.5413 10.3453 18.2615 10.6252 17.9163 10.6252H16.333C15.9878 10.6252 15.708 10.3453 15.708 10.0002ZM9.99967 15.7085C10.3449 15.7085 10.6247 15.9883 10.6247 16.3335V17.9168C10.6247 18.262 10.3449 18.5418 9.99967 18.5418C9.6545 18.5418 9.37467 18.262 9.37467 17.9168V16.3335C9.37467 15.9883 9.6545 15.7085 9.99967 15.7085Z" fill="black" />
                                </svg>
                                <?= $arElement["PROPERTY_ROOMS_VALUE"] . ' ' . $roomsDeclension->get($arElement["PROPERTY_ROOMS_VALUE"]) ?>
                            </div>
                        <? endif; ?>
                        <? $text = plural_form($guests, array('взрослый на основном месте', 'взрослых на основных местах', 'взрослых на основных местах')); ?>
                        <? if (count($arTariff['variants'])) { ?>
                            <div class="room__variants">
                                <? if (count($arTariff['variants']) > 1) { ?>
                                    <p class="room__variants-title">Выберите вариант размещения</p>
                                <? } ?>
                                <? foreach ($arTariff['variants'] as $key => $variant) { ?>
                                    <?
                                    if (isset($arTariff['seatDispence']['childrenIsAdults'])) {
                                        if (($arTariff['seatDispence']['main'] - $arTariff['seatDispence']['childrenIsAdults'] - $arTariff['seatDispence']['extra']) != 0) {
                                            $variantName = plural_form($arTariff['seatDispence']['main'] - $arTariff['seatDispence']['childrenIsAdults'] - $arTariff['seatDispence']['extra'], array('взрослый на основном месте', 'взрослых на основных местах', 'взрослых на основных местах')) . ', ' . plural_form($arTariff['seatDispence']['childrenIsAdults'], array('ребёнок на основном месте', 'детей на основных местах', 'детей на основных местах')) . '<br>' . $variant['NAME'];
                                        } else {
                                            $variantName = plural_form($arTariff['seatDispence']['childrenIsAdults'], array('ребёнок на основном месте', 'детей на основных местах', 'детей на основных местах')) . '<br>' . $variant['NAME'];
                                        }
                                    } else {
                                        $variantName = (isset($arTariff['seatDispence']['extra']) ? plural_form($guests - $arTariff['seatDispence']['extra'], array('взрослый на основном месте', 'взрослых на основных местах', 'взрослых на основных местах')) : $text) . '<br>' . $variant['NAME'];
                                    }
                                    ?>
                                    <div class="room__variant <?= count($arTariff['variants']) == 1 ? 'single' : '' ?>">
                                        <label class="checkbox room__variant-check">
                                            <input onchange="markupSelectHandler(this);" type="radio" class="checkbox" value="<?= $variant['PRICE'] ?>" name="<?= $arElement["PROPERTY_EXTERNAL_ID_VALUE"] ?>" <?= $key == 0 ? 'checked' : '' ?>>
                                            <span></span>
                                            <div class="room__variant-text">
                                                <div>
                                                    <?= count($arTariff['variants']) > 1 ? $variantName : str_replace('<br>', ', ', $variantName) ?><br>
                                                </div>
                                                <b><?= number_format($variant['PRICE'], 0, '.', ' ') ?> ₽</b>
                                            </div>
                                        </label>
                                    </div>
                                <? } ?>
                            </div>
                        <? } ?>
                        <?
                        if ($children > 0) {
                            if (!empty($arSection['UF_MIN_AGE'])) {
                                $miniChildren = 0;
                                $bigChildren = 0;
                                $arChildrenAges = explode(',', $_GET['childrenAge']);
                                foreach ($arChildrenAges as $key => $age) {
                                    if ($age <= $arSection['UF_MIN_AGE']) {
                                        $miniChildren++;
                                    } else {
                                        $bigChildren++;
                                    }
                                }
                                if (!empty($miniChildren)) {
                                    $text .= ', ' . $miniChildren . ' ' . $childrenDeclension->get($miniChildren) . ' без места';
                                }

                                if (!empty($bigChildren)) {
                                    $text .= ', ' . plural_form($bigChildren, array('ребенок на основном месте', 'детей на основных местах', 'детей на основных местах'));
                                }
                            } else {
                                $text .= ', ' . plural_form($children, array('ребенок на основном месте', 'детей на основных местах', 'детей на основных местах'));
                            }
                        }
                        $text .= '.';
                        ?>
                        <? if (!count($arTariff['variants'])) { ?>
                            <div class="room__features">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.75137 12.5754C3.45346 9.49595 5.66865 6.0415 9.00166 6.0415H10.9979C14.3309 6.0415 16.5461 9.49595 15.2482 12.5754C14.9949 13.1763 14.4095 13.5736 13.7537 13.5736H13.1374L12.2516 17.1764C11.9958 18.217 11.072 18.9582 9.99977 18.9582C8.92752 18.9582 8.00373 18.217 7.7479 17.1764L6.86209 13.5736H6.24589C5.59004 13.5736 5.00465 13.1763 4.75137 12.5754ZM9.00166 7.2915C6.59481 7.2915 4.9401 9.80474 5.90324 12.0899C5.96492 12.2362 6.10217 12.3236 6.24589 12.3236H6.94887C7.48002 12.3236 7.93216 12.6903 8.05675 13.1971L8.96175 16.878C9.08333 17.3725 9.51593 17.7082 9.99977 17.7082C10.4836 17.7082 10.9162 17.3725 11.0378 16.878L11.9428 13.1971C12.0674 12.6903 12.5195 12.3236 13.0507 12.3236H13.7537C13.8974 12.3236 14.0346 12.2362 14.0963 12.0899C15.0594 9.80474 13.4047 7.2915 10.9979 7.2915H9.00166Z" fill="#141B34" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.2915C8.96447 2.2915 8.125 3.13097 8.125 4.1665C8.125 5.20204 8.96447 6.0415 10 6.0415C11.0355 6.0415 11.875 5.20204 11.875 4.1665C11.875 3.13097 11.0355 2.2915 10 2.2915ZM6.875 4.1665C6.875 2.44061 8.27411 1.0415 10 1.0415C11.7259 1.0415 13.125 2.44061 13.125 4.1665C13.125 5.89239 11.7259 7.2915 10 7.2915C8.27411 7.2915 6.875 5.89239 6.875 4.1665Z" fill="#141B34" />
                                </svg>
                                <?= $text; ?>
                            </div>
                        <? } ?>
                        <? if (!empty($arElement["PROPERTY_BEDS_VALUE"])): ?>
                            <div class="room__features">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.04199 14.5835C1.04199 14.2383 1.32181 13.9585 1.66699 13.9585L18.3337 13.9585C18.6788 13.9585 18.9587 14.2383 18.9587 14.5835C18.9587 14.9287 18.6788 15.2085 18.3337 15.2085L1.66699 15.2085C1.32181 15.2085 1.04199 14.9287 1.04199 14.5835Z" fill="#141B34" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.95699 9.375L15.0437 9.375C15.7924 9.37498 16.4167 9.37496 16.9124 9.44159C17.4356 9.51193 17.9079 9.66666 18.2874 10.0462C18.667 10.4258 18.8217 10.8981 18.8921 11.4213C18.9587 11.9169 18.9587 12.5413 18.9587 13.29L18.9587 17.5C18.9587 17.8452 18.6788 18.125 18.3337 18.125C17.9885 18.125 17.7087 17.8452 17.7087 17.5L17.7087 13.3333C17.7087 12.53 17.7073 11.9904 17.6532 11.5878C17.6015 11.2033 17.5122 11.0387 17.4036 10.9301C17.295 10.8215 17.1304 10.7321 16.7458 10.6804C16.3433 10.6263 15.8037 10.625 15.0003 10.625L5.00033 10.625C4.19698 10.625 3.65735 10.6263 3.25482 10.6804C2.87027 10.7321 2.70569 10.8215 2.59709 10.9301C2.48849 11.0387 2.39914 11.2033 2.34744 11.5878C2.29332 11.9904 2.29199 12.53 2.29199 13.3333L2.29199 17.5C2.29199 17.8452 2.01217 18.125 1.66699 18.125C1.32182 18.125 1.04199 17.8452 1.04199 17.5L1.04199 13.29C1.04197 12.5413 1.04195 11.9169 1.10859 11.4213C1.17892 10.8981 1.33365 10.4258 1.71321 10.0462C2.09277 9.66666 2.5651 9.51193 3.08826 9.44159C3.58391 9.37496 4.20826 9.37498 4.95699 9.375Z" fill="#141B34" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.62506 8.49478C5.62502 8.5 5.625 8.50545 5.625 8.51113L5.625 10C5.625 10.3452 5.34518 10.625 5 10.625C4.65482 10.625 4.375 10.3452 4.375 10L4.375 8.51113C4.375 8.34805 4.38114 8.10535 4.4996 7.87706C4.6318 7.62227 4.84491 7.4895 5.00705 7.40649C5.57701 7.11468 6.2925 6.875 7.08333 6.875C7.87417 6.875 8.58966 7.11468 9.15962 7.40649C9.32175 7.4895 9.53487 7.62227 9.66707 7.87706C9.78553 8.10535 9.79167 8.34805 9.79167 8.51113V10C9.79167 10.3452 9.51185 10.625 9.16667 10.625C8.82149 10.625 8.54167 10.3452 8.54167 10V8.51113C8.54167 8.50545 8.54165 8.5 8.54161 8.49478C8.10552 8.27855 7.60423 8.125 7.08333 8.125C6.56244 8.125 6.06114 8.27855 5.62506 8.49478Z" fill="#141B34" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.459 8.49478C11.459 8.5 11.459 8.50545 11.459 8.51113V10C11.459 10.3452 11.1792 10.625 10.834 10.625C10.4888 10.625 10.209 10.3452 10.209 10V8.51113C10.209 8.34805 10.2151 8.10535 10.3336 7.87706C10.4658 7.62227 10.6789 7.4895 10.841 7.40649C11.411 7.11468 12.1265 6.875 12.9173 6.875C13.7082 6.875 14.4236 7.11468 14.9936 7.40649C15.1557 7.4895 15.3689 7.62227 15.5011 7.87706C15.6195 8.10535 15.6257 8.34805 15.6257 8.51113V10C15.6257 10.3452 15.3458 10.625 15.0007 10.625C14.6555 10.625 14.3757 10.3452 14.3757 10V8.51113C14.3757 8.50545 14.3756 8.5 14.3756 8.49478C13.9395 8.27855 13.4382 8.125 12.9173 8.125C12.3964 8.125 11.8951 8.27855 11.459 8.49478Z" fill="#141B34" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0003 3.125C7.69917 3.125 5.57341 3.75972 3.83328 4.83415C3.33922 5.13921 3.26513 5.20174 3.19907 5.31403C3.14362 5.40828 3.12527 5.49888 3.12527 6.13381L3.12527 10C3.12527 10.3452 2.84545 10.625 2.50027 10.625C2.15509 10.625 1.87527 10.3452 1.87527 10L1.87527 6.13381C1.87527 6.10217 1.8752 6.07066 1.87514 6.03926C1.87409 5.55678 1.8731 5.10273 2.1217 4.68018L2.66038 4.99711L2.1217 4.68018C2.36196 4.27179 2.71665 4.05357 3.10826 3.81264C3.13091 3.7987 3.15369 3.78469 3.17657 3.77056C5.11571 2.57324 7.46916 1.875 10.0003 1.875C12.5314 1.875 14.8848 2.57324 16.824 3.77056C16.8469 3.78469 16.8696 3.7987 16.8923 3.81264C17.2839 4.05357 17.6386 4.27179 17.8789 4.68018C18.1274 5.10273 18.1265 5.55678 18.1254 6.03926C18.1253 6.07066 18.1253 6.10217 18.1253 6.13381L18.1253 10C18.1253 10.3452 17.8455 10.625 17.5003 10.625C17.1551 10.625 16.8753 10.3452 16.8753 10V6.13381C16.8753 5.49888 16.8569 5.40828 16.8015 5.31403C16.7354 5.20174 16.6613 5.13921 16.1673 4.83415C14.4271 3.75972 12.3014 3.125 10.0003 3.125Z" fill="#141B34" />
                                </svg>
                                <?= $arElement["PROPERTY_BEDS_VALUE"] . ' ' . $bedsDeclension->get($arElement["PROPERTY_BEDS_VALUE"]) ?>
                            </div>
                        <? endif; ?>
                        <? if (in_array(4, $arSection['UF_REST_VARIANTS'])) { ?>
                            <div class="room__features">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M16.0783 6.73408C16.489 6.6994 16.9388 6.81365 17.26 7.0783C17.6796 7.42405 17.8902 7.93488 17.9368 8.46869C17.999 9.1818 17.7269 9.92307 17.272 10.4673C16.8783 10.9383 16.3122 11.3411 15.6852 11.3974L15.6689 11.3997C15.2211 11.4579 14.8056 11.3243 14.4532 11.0474C14.0974 10.768 13.8637 10.2919 13.8114 9.84627C13.719 9.05996 13.9614 8.31129 14.4504 7.69356C14.852 7.18631 15.4274 6.8102 16.0783 6.73408Z" fill="black" />
                                    <path d="M3.76666 6.73486C4.33871 6.75461 4.85266 6.98449 5.26748 7.37863C5.81668 7.90045 6.16543 8.67266 6.18123 9.43256C6.19229 9.96441 6.06635 10.5317 5.68445 10.9259C5.44852 11.1694 5.14127 11.3371 4.8061 11.3942C4.69219 11.4135 4.57651 11.4145 4.46127 11.4163C4.34637 11.41 4.23416 11.402 4.12129 11.3781C3.71096 11.2914 3.33276 11.0712 3.02106 10.7959C2.45014 10.2916 2.08543 9.53889 2.04287 8.77859C2.0124 8.23451 2.1636 7.66867 2.53529 7.25875C2.86014 6.90049 3.29604 6.7575 3.76666 6.73486Z" fill="black" />
                                    <path d="M7.23097 2.81811C7.69628 2.78835 8.15477 3.00952 8.49698 3.30966C9.097 3.83593 9.45569 4.65901 9.50024 5.45087C9.53844 6.13017 9.33649 6.81059 8.87407 7.31767C8.56679 7.6546 8.16737 7.85595 7.71057 7.87843C7.23003 7.90276 6.78227 7.69733 6.4228 7.38977C5.82263 6.87624 5.50028 6.06485 5.44325 5.28974C5.39556 4.64151 5.56805 3.95249 5.9963 3.45343C6.31889 3.07751 6.73768 2.85587 7.23097 2.81811Z" fill="black" />
                                    <path d="M12.5465 2.81814C12.9555 2.78245 13.3691 2.90378 13.6952 3.15409C14.1953 3.53808 14.4702 4.1198 14.5514 4.73704C14.6577 5.5455 14.4248 6.3739 13.9288 7.01804C13.5929 7.45429 13.1237 7.7778 12.5713 7.85151C12.1253 7.89903 11.7053 7.78026 11.3483 7.50831C10.8674 7.14194 10.6086 6.56173 10.5317 5.97349C10.4265 5.16825 10.6743 4.29138 11.1702 3.65054C11.5186 3.20034 11.9762 2.89085 12.5465 2.81814Z" fill="black" />
                                    <path d="M9.73585 8.39486C10.3852 8.32212 11.1575 8.54968 11.6915 8.91794C12.1896 9.26142 12.5765 9.72031 12.842 10.2635C12.9355 10.4549 13.0162 10.6528 13.1142 10.842C13.214 11.0349 13.3387 11.2259 13.4718 11.3974C14.1384 12.2563 15.3137 12.7368 15.5235 13.9019C15.6106 14.3856 15.503 14.8623 15.3256 15.3121C15.2181 15.5845 15.0829 15.8521 14.9137 16.0916C14.5688 16.5796 14.0571 17.0413 13.4527 17.1544C13.2892 17.1849 13.1081 17.1683 12.9449 17.141C12.5753 17.0791 12.206 16.9374 11.8702 16.7739C11.3614 16.5261 10.8798 16.2298 10.3108 16.1436C9.93774 16.0991 9.58503 16.1411 9.22905 16.2608C9.01642 16.3323 8.81462 16.435 8.61397 16.5345C8.08188 16.7982 7.56222 17.0733 6.96343 17.1429L6.91515 17.1483C6.42425 17.1895 5.98438 16.9862 5.61472 16.6764C4.98138 16.1455 4.54569 15.3331 4.47567 14.5082C4.44884 14.1922 4.48159 13.8702 4.59114 13.5715C4.73005 13.1926 4.98956 12.8649 5.27011 12.5801C5.46823 12.379 5.68644 12.1951 5.89503 12.0048C6.1353 11.7855 6.37394 11.5625 6.56495 11.2974C6.823 10.9395 6.99419 10.5434 7.20456 10.1583C7.34524 9.90085 7.50761 9.65652 7.69835 9.4333C8.07276 8.99507 8.6278 8.64117 9.18235 8.4872C9.36466 8.4366 9.54772 8.41048 9.73585 8.39486Z" fill="black" />
                                </svg>
                                Можно с животными
                            </div>
                        <? } ?>
                        <a class="room__features-more" elementId="<?= $arElement['ID'] ?>" href="#" data-room-more="<?= $arElement['ID'] . '-' . $arTariff['tariffId'] ?>">Подробнее о номере</a>
                    </div>
                </div>
            </div>
            <? if ($arTariff['price']): ?>
                <? $elementOldPrice = 0; ?>
                <? if ($arElement['DISCOUNT_DATA']) {
                    if ($arElement['DISCOUNT_DATA']['VALUE_TYPE'] == 'P') {
                        $elementPrice = $arTariff['price'] * (100 - $arElement['DISCOUNT_DATA']['VALUE']) / 100;
                    } else {
                        $elementPrice = $arTariff['price'] - $arElement['DISCOUNT_DATA']['VALUE'];
                    }
                    $elementOldPrice = $arTariff['price'];
                } else {
                    $elementPrice = $arTariff['price'];
                } ?>
                <div class="room__order">

                    <?php /* if ($USER->IsAdmin()): ?>
                                    <?php if (
                                        $elementPrice > Users::getInnerScore()
                                        && intval(Users::getInnerScore()) !== 0
                                        && $isAuthorized
                                    ): ?>
                                        <div class="room__price_cert_price">
                                            <div class="room__price_cert_price-item">
                                                <span>Доплата</span>
                                                <span>
                                                    <?= number_format($elementPrice - Users::getInnerScore(), 0, '.', ' ') ?> ₽
                                                </span>
                                            </div>
                                        </div>
                                    <? endif; ?>
                                <? endif; */ ?>

                    <div class="room__left">
                        <? $cancelation = []; ?>
                        <? if (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '2') { ?>
                            <? if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                                array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                            } else {
                                array_push($cancelation, 'Штраф за отмену бронирования — ' . $arTariff['price'] * ($arTariff['value']['PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE'] / 100) . ' ₽');
                            } ?>
                        <? } elseif (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '5') { ?>
                            <? if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                                array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                            }
                            array_push($cancelation, 'Штраф за отмену бронирования — ' . $arTariff['price'] . ' ₽');
                            ?>
                        <? } elseif (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '4') { ?>
                            <? if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                                array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                            }
                            array_push($cancelation, 'Штраф за отмену бронирования — ' . array_shift($arTariff['prices']) . ' ₽');
                            ?>
                        <? } elseif (!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'])) { ?>
                            <? if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                                array_push($cancelation,  $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                            }
                            array_push($cancelation,  'Штраф за отмену бронирования — ' . $arTariff['value']['PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE'] . ' ₽');
                            ?>
                        <? } else { ?>
                            <? if (!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'])) {
                                array_push($cancelation, $arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']);
                            }
                            array_push($cancelation, 'Бесплатная отмена бронирования'); ?>
                        <? } ?>
                        <? if (count($cancelation)) { ?>
                            <div class="room__cancelation">
                                <div class="room__cancelation-title">
                                    Условия отмены
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                        <path d="M9 17.0625C4.5525 17.0625 0.9375 13.4475 0.9375 9C0.9375 4.5525 4.5525 0.9375 9 0.9375C13.4475 0.9375 17.0625 4.5525 17.0625 9C17.0625 13.4475 13.4475 17.0625 9 17.0625ZM9 2.0625C5.175 2.0625 2.0625 5.175 2.0625 9C2.0625 12.825 5.175 15.9375 9 15.9375C12.825 15.9375 15.9375 12.825 15.9375 9C15.9375 5.175 12.825 2.0625 9 2.0625Z" fill="black" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.5625C8.43899 6.5625 8.0625 6.97212 8.0625 7.38462C8.0625 7.69528 7.81066 7.94712 7.5 7.94712C7.18934 7.94712 6.9375 7.69528 6.9375 7.38462C6.9375 6.26771 7.90415 5.4375 9 5.4375C10.0958 5.4375 11.0625 6.26771 11.0625 7.38462C11.0625 7.78173 10.9362 8.14981 10.7238 8.45453C10.5926 8.64269 10.4397 8.82172 10.3 8.98201C10.2743 9.01149 10.2491 9.04031 10.2243 9.06858C10.1083 9.2011 10.0026 9.32194 9.90482 9.44595C9.66069 9.75567 9.5625 9.97137 9.5625 10.1538V10.5C9.5625 10.8107 9.31066 11.0625 9 11.0625C8.68934 11.0625 8.4375 10.8107 8.4375 10.5V10.1538C8.4375 9.57162 8.74634 9.09836 9.0213 8.74953C9.13876 8.60052 9.26748 8.45354 9.38384 8.32067C9.40711 8.29411 9.42987 8.26812 9.45196 8.24278C9.59095 8.08333 9.70791 7.94456 9.80089 7.81118C9.88929 7.68437 9.9375 7.53879 9.9375 7.38462C9.9375 6.97212 9.56101 6.5625 9 6.5625Z" fill="black" />
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.4375 12.375C8.4375 12.0643 8.68934 11.8125 9 11.8125H9.00674C9.3174 11.8125 9.56924 12.0643 9.56924 12.375C9.56924 12.6857 9.3174 12.9375 9.00674 12.9375H9C8.68934 12.9375 8.4375 12.6857 8.4375 12.375Z" fill="black" />
                                    </svg>
                                </div>
                                <div class="room__cancelation-tooltip">
                                    <div class="room__cancelation-tooltip-title">Условия отмены бронирования</div>
                                    <ul>
                                        <? foreach ($cancelation as $calcel) { ?>
                                            <li><?= $calcel ?></li>
                                        <? } ?>
                                    </ul>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="5" viewBox="0 0 10 5" fill="none">
                                        <path d="M9.5 0L5 5L0.5 0H9.5Z" fill="#E0C695" />
                                    </svg>
                                </div>
                            </div>
                        <? } ?>
                    </div>

                    <div class="room__price">
                        <div class="room__price-per-night">
                            <span class="room__final-price"><?= number_format($elementPrice, 0, '.', ' ') ?> <span>₽</span></span>
                            <? if ($elementOldPrice) { ?>
                                <span class="room__old-price"><span class="number"><?= number_format($elementOldPrice, 0, '.', ' ') ?></span> <span class="rub">₽</span></span>
                            <? } ?>
                            <span class="room__nights">за <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?></span>
                        </div>
                        <div class="split-wrap" <?= $elementPrice - Users::getInnerScore() <= 0 ? 'style="display: none"' : '' ?>>
                            <yandex-pay-badge
                                merchant-id="d82873ad-61ce-4050-b05e-1f4599f0bb7b"
                                type="bnpl"
                                amount="<?= $elementPrice - Users::getInnerScore() ?>"
                                size="l"
                                variant="detailed"
                                theme="light"
                                align="left"
                                color="transparent" />
                        </div>
                    </div>

                    <a class="button button_primary"
                        onclick="VK.Goal('customize_product')"
                        data-section-external-id="<?= $arSection['UF_EXTERNAL_ID'] ?>"
                        data-add-basket
                        data-object-title="<?= $arSection['NAME'] ?>"
                        data-id="<?= $arElement["ID"] ?>"
                        data-price="<?= $arTariff['price'] ?>"
                        data-guests="<?= $guests ?>"
                        data-children-age="<?= $_GET['childrenAge'] ?>"
                        data-date-from="<?= $dateFrom ?>"
                        data-date-to="<?= $dateTo ?>"
                        data-external-id="<?= $arElement["PROPERTY_EXTERNAL_ID_VALUE"] ?>"
                        data-external-service="<?= $arSection["UF_EXTERNAL_SERVICE"] ?>"
                        data-tariff-id='<?= $arTariff['tariffId'] ?>'
                        data-category-id="<?= $arTariff['categoryId'] ?>"
                        data-prices='<?= serialize($arTariff['prices']) ?>'
                        data-cancel-amount="<?= $arTariff['cancelAmount'] ?>"
                        data-people="<?= $text ?>"
                        data-room-title="<?= $arElement["NAME"] . ' ' . ($arTariff['value']['PROPERTY_NAME_DETAIL_VALUE'] ?? $arTariff['value']['NAME']) ?>"
                        data-room-photo="<?= $arElement["PICTURES"][array_key_first($arElement["PICTURES"])]['src'] ?>"
                        href="#">Забронировать</a>
                </div>
            <? endif; ?>
        </div>
    <? endforeach; ?>
<? endforeach;

foreach ($arElements as $key => $arElement):
    if ($key == 0) { ?>
        <div class="rooms__empty-list">
            <div>Не осталось свободных мест</div>
        </div>
    <? }

    if ($arElement['AVAILABLE_ID'] == true):
        continue;
    endif; ?>
    <div class="room room__empty">
        <div class="room__top">
            <? if ($arElement["PICTURES"]): ?>
                <div class="room__images">
                    <? if ($arElement['PROPERTY_ROOMTOUR_VALUE']) { ?>
                        <a class="room__tour" href="<?= CFile::GetPath($arElement['PROPERTY_ROOMTOUR_VALUE']) ?>" data-fancybox="gallery_<?= $arElement['ID'] ?>" data-caption="<?= $arElement["NAME"] ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M11.0471 1.5H6.95215V4.77H11.0471V1.5Z" fill="black" />
                                <path d="M12.1729 1.5V4.77H16.4029C16.0204 2.7075 14.4979 1.5075 12.1729 1.5Z" fill="black" />
                                <path d="M1.5 5.89502V12.1425C1.5 14.8725 3.1275 16.5 5.8575 16.5H12.1425C14.8725 16.5 16.5 14.8725 16.5 12.1425V5.89502H1.5ZM10.83 12.135L9.27 13.035C8.94 13.2225 8.6175 13.32 8.3175 13.32C8.0925 13.32 7.89 13.2675 7.7025 13.1625C7.2675 12.915 7.0275 12.405 7.0275 11.745V9.94502C7.0275 9.28502 7.2675 8.77502 7.7025 8.52752C8.1375 8.27252 8.6925 8.31752 9.27 8.65502L10.83 9.55502C11.4075 9.88502 11.7225 10.35 11.7225 10.8525C11.7225 11.355 11.4 11.7975 10.83 12.135Z" fill="black" />
                                <path d="M5.82766 1.5C3.50266 1.5075 1.98016 2.7075 1.59766 4.77H5.82766V1.5Z" fill="black" />
                            </svg>
                            Румтур
                        </a>
                    <? } ?>
                    <div class="swiper slider-gallery" data-slider-object="data-slider-object">
                        <div class="swiper-wrapper">
                            <? $keyPhoto = 1; ?>
                            <? foreach ($arElement["PICTURES"] as $arPhoto): ?>
                                <? if (count($arElement["PICTURES"]) > 1): ?>
                                    <?
                                    $alt = $arResult["arSection"]["NAME"] . " " . $arElement["NAME"] . " рис." . $keyPhoto;;
                                    $title = "Фото - " . $arElement["NAME"] . " рис." . $keyPhoto;
                                    ?>
                                <? else: ?>
                                    <?
                                    $alt = $arResult["arSection"]["NAME"] . " " . $arElement["NAME"];
                                    $title = "Фото - " . $arElement["NAME"];
                                    ?>
                                <? endif; ?>
                                <div class="swiper-slide">
                                    <a href="<?= $arPhoto["big"] ?>" data-fancybox="gallery_<?= $arElement['ID'] ?>" data-caption="<?= $arElement["NAME"] ?>">
                                        <img class="" loading="lazy" alt="<?= $alt; ?>" title="<?= $title; ?>" src="<?= $arPhoto["src"] ?>">
                                    </a>
                                </div>
                                <? $keyPhoto++; ?>
                            <? endforeach; ?>
                        </div>

                        <? if (count($arElement["PICTURES"]) > 1): ?>
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
                </div>
                <script>
                    Fancybox.bind(' [data-fancybox="gallery_<?= $arElement['ID'] ?>" ]', {
                        Toolbar: {
                            display: {
                                left: ["infobar"],
                                middle: [],
                                right: ["close"],
                            },
                        },

                        commonCaption: true,

                        Thumbs: {
                            type: "classic",
                        },
                    });
                </script>
            <? endif; ?>
            <div class="room__content">
                <div class="room__description">
                    <div class="room__description-title"><?= $arElement["NAME"] ?></div>
                    <? if (!empty($arElement["PROPERTY_SQUARE_VALUE"])): ?>
                        <div class="room__features">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10.0001 18.9583C9.95013 18.9583 9.90013 18.95 9.85846 18.9417C9.80846 18.9333 9.75846 18.9083 9.70846 18.8833L7.8668 17.9667C7.55847 17.8083 7.43346 17.4333 7.58346 17.125C7.7418 16.8167 8.1168 16.6917 8.42513 16.8417L9.37513 17.3167V16.0167C9.37513 15.675 9.65846 15.3917 10.0001 15.3917C10.3418 15.3917 10.6251 15.675 10.6251 16.0167V17.3167L11.5751 16.8417C11.8835 16.6917 12.2585 16.8167 12.4168 17.125C12.5751 17.4333 12.4501 17.8083 12.1335 17.9667L10.2918 18.8833C10.2418 18.9083 10.1918 18.925 10.1418 18.9417C10.0918 18.95 10.0501 18.9583 10.0001 18.9583ZM15.5585 16.1833C15.3335 16.1833 15.1085 16.0583 15.0001 15.8417C14.8418 15.5333 14.9668 15.1583 15.2835 15L16.7918 14.25V12.325C16.7918 11.9833 17.0751 11.7 17.4168 11.7C17.7585 11.7 18.0418 11.9833 18.0418 12.325V14.6417C18.0418 14.875 17.9085 15.0917 17.7001 15.2L15.8501 16.125C15.7418 16.1583 15.6501 16.1833 15.5585 16.1833ZM4.4418 16.1833C4.35013 16.1833 4.25013 16.1583 4.1668 16.1167L2.3168 15.1917C2.10846 15.0833 1.97513 14.8667 1.97513 14.6333V12.3167C1.97513 11.975 2.25847 11.6917 2.60013 11.6917C2.9418 11.6917 3.22513 11.975 3.22513 12.3167V14.2417L4.73346 14.9917C5.0418 15.15 5.16679 15.525 5.01679 15.8333C4.89179 16.05 4.67513 16.1833 4.4418 16.1833ZM10.0001 12.0167C9.65846 12.0167 9.37513 11.7333 9.37513 11.3917V9.46666L7.8668 8.71667C7.55847 8.55833 7.43346 8.18332 7.58346 7.87499C7.7418 7.56666 8.1168 7.44166 8.42513 7.59166L10.0001 8.375L11.5751 7.59166C11.8835 7.44166 12.2585 7.55832 12.4168 7.87499C12.5751 8.19166 12.4501 8.55833 12.1335 8.71667L10.6251 9.46666V11.3917C10.6251 11.7333 10.3418 12.0167 10.0001 12.0167ZM17.4085 8.30833C17.0668 8.30833 16.7835 8.02499 16.7835 7.68333V6.38333L15.8335 6.85832C15.5251 7.01666 15.1501 6.89166 14.9918 6.57499C14.8335 6.26666 14.9585 5.89166 15.2751 5.73333L16.0085 5.36666L15.2751 4.99999C14.9668 4.84166 14.8418 4.46666 14.9918 4.15833C15.1501 3.85 15.5251 3.72499 15.8335 3.87499L17.6751 4.79166C17.6918 4.79999 17.7168 4.80832 17.7335 4.82499C17.7835 4.84999 17.8251 4.89166 17.8668 4.93333C17.8918 4.96666 17.9168 4.99999 17.9418 5.03332C17.9751 5.09165 18.0001 5.14999 18.0168 5.21666C18.0251 5.26666 18.0335 5.31665 18.0335 5.35832V5.36666V7.66666C18.0335 8.03332 17.7501 8.30833 17.4085 8.30833ZM2.5918 8.30833C2.25013 8.30833 1.9668 8.02499 1.9668 7.68333V5.38333V5.37499C1.9668 5.32499 1.97513 5.27499 1.98346 5.23333C2.00013 5.16666 2.02513 5.10833 2.05846 5.04999C2.08346 5.00832 2.10846 4.97499 2.1418 4.94165C2.17513 4.90832 2.2168 4.875 2.25846 4.85C2.27513 4.84167 2.30013 4.82499 2.3168 4.81666L4.15846 3.89999C4.46679 3.74999 4.8418 3.86666 5.00013 4.18333C5.15846 4.5 5.03347 4.86666 4.7168 5.02499L3.98346 5.39166L4.7168 5.75833C5.02513 5.91666 5.15013 6.29166 5.00013 6.59999C4.8418 6.90833 4.47513 7.03332 4.15846 6.88332L3.20846 6.40833V7.70833C3.2168 8.03333 2.9418 8.30833 2.5918 8.30833ZM11.8501 3.21666C11.7585 3.21666 11.6585 3.19166 11.5751 3.14999L10.0001 2.36666L8.42513 3.14999C8.1168 3.30833 7.7418 3.18333 7.58346 2.86666C7.42513 2.55833 7.55013 2.18332 7.8668 2.02499L9.7168 1.1C9.8918 1.00833 10.1001 1.00833 10.2751 1.1L12.1251 2.02499C12.4335 2.18332 12.5585 2.55833 12.4085 2.86666C12.3001 3.09166 12.0835 3.21666 11.8501 3.21666Z" fill="black" />
                            </svg>
                            <?= $arElement["PROPERTY_SQUARE_VALUE"] ?> м²
                        </div>
                    <? endif; ?>
                    <? if (!empty($arElement["PROPERTY_ROOMS_VALUE"])): ?>
                        <div class="room__features">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.95203 1.4585H10.0473C11.8729 1.45848 13.3068 1.45848 14.4263 1.60899C15.5735 1.76323 16.4837 2.08576 17.1989 2.80092C17.9141 3.51609 18.2366 4.4263 18.3908 5.57352C18.5414 6.69303 18.5414 8.12693 18.5413 9.95252V10.0478C18.5414 11.8734 18.5414 13.3073 18.3908 14.4268C18.2366 15.574 17.9141 16.4842 17.1989 17.1994C16.4837 17.9146 15.5735 18.2371 14.4263 18.3913C13.3068 18.5418 11.8729 18.5418 10.0473 18.5418H9.95204C8.12644 18.5418 6.69254 18.5418 5.57303 18.3913C4.42581 18.2371 3.5156 17.9146 2.80044 17.1994C2.08527 16.4842 1.76274 15.574 1.6085 14.4268C1.45799 13.3073 1.458 11.8734 1.45801 10.0478V9.95252C1.458 8.12693 1.45799 6.69303 1.6085 5.57352C1.76274 4.4263 2.08527 3.51609 2.80044 2.80092C3.5156 2.08576 4.42581 1.76323 5.57303 1.60899C6.69254 1.45848 8.12644 1.45848 9.95203 1.4585ZM5.73959 2.84784C4.73098 2.98345 4.12852 3.2406 3.68432 3.68481C3.24012 4.12901 2.98296 4.73147 2.84736 5.74008C2.70934 6.76666 2.70801 8.11652 2.70801 10.0002C2.70801 11.8838 2.70934 13.2337 2.84736 14.2602C2.98296 15.2689 3.24012 15.8713 3.68432 16.3155C4.12852 16.7597 4.73098 17.0169 5.73959 17.1525C6.76617 17.2905 8.11603 17.2918 9.99967 17.2918C11.8833 17.2918 13.2332 17.2905 14.2598 17.1525C15.2684 17.0169 15.8708 16.7597 16.315 16.3155C16.7592 15.8713 17.0164 15.2689 17.152 14.2602C17.29 13.2337 17.2913 11.8838 17.2913 10.0002C17.2913 8.11652 17.29 6.76666 17.152 5.74008C17.0164 4.73147 16.7592 4.12901 16.315 3.68481C15.8708 3.2406 15.2684 2.98345 14.2598 2.84784C13.2332 2.70982 11.8833 2.7085 9.99967 2.7085C8.11603 2.7085 6.76617 2.70982 5.73959 2.84784Z" fill="black" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99967 1.4585C10.3449 1.4585 10.6247 1.73832 10.6247 2.0835V3.66683C10.6247 4.01201 10.3449 4.29183 9.99967 4.29183C9.6545 4.29183 9.37467 4.01201 9.37467 3.66683V2.0835C9.37467 1.73832 9.6545 1.4585 9.99967 1.4585ZM9.99967 7.00015C10.3449 7.00015 10.6247 7.27998 10.6247 7.62515V9.37515H12.3747C12.7199 9.37515 12.9997 9.65498 12.9997 10.0002C12.9997 10.3453 12.7199 10.6252 12.3747 10.6252H10.6247V12.3752C10.6247 12.7203 10.3449 13.0002 9.99967 13.0002C9.6545 13.0002 9.37467 12.7203 9.37467 12.3752V10.6252H7.62467C7.2795 10.6252 6.99967 10.3453 6.99967 10.0002C6.99967 9.65498 7.2795 9.37515 7.62467 9.37515H9.37467V7.62515C9.37467 7.27998 9.6545 7.00015 9.99967 7.00015ZM1.45801 10.0002C1.45801 9.65498 1.73783 9.37516 2.08301 9.37516H3.66634C4.01152 9.37516 4.29134 9.65498 4.29134 10.0002C4.29134 10.3453 4.01152 10.6252 3.66634 10.6252H2.08301C1.73783 10.6252 1.45801 10.3453 1.45801 10.0002ZM15.708 10.0002C15.708 9.65498 15.9878 9.37516 16.333 9.37516H17.9163C18.2615 9.37516 18.5413 9.65498 18.5413 10.0002C18.5413 10.3453 18.2615 10.6252 17.9163 10.6252H16.333C15.9878 10.6252 15.708 10.3453 15.708 10.0002ZM9.99967 15.7085C10.3449 15.7085 10.6247 15.9883 10.6247 16.3335V17.9168C10.6247 18.262 10.3449 18.5418 9.99967 18.5418C9.6545 18.5418 9.37467 18.262 9.37467 17.9168V16.3335C9.37467 15.9883 9.6545 15.7085 9.99967 15.7085Z" fill="black" />
                            </svg>
                            <?= $arElement["PROPERTY_ROOMS_VALUE"] . ' ' . $roomsDeclension->get($arElement["PROPERTY_ROOMS_VALUE"]) ?>
                        </div>
                    <? endif; ?>
                    <? if (false): ?>
                        <div class="room__features">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.75137 12.5754C3.45346 9.49595 5.66865 6.0415 9.00166 6.0415H10.9979C14.3309 6.0415 16.5461 9.49595 15.2482 12.5754C14.9949 13.1763 14.4095 13.5736 13.7537 13.5736H13.1374L12.2516 17.1764C11.9958 18.217 11.072 18.9582 9.99977 18.9582C8.92752 18.9582 8.00373 18.217 7.7479 17.1764L6.86209 13.5736H6.24589C5.59004 13.5736 5.00465 13.1763 4.75137 12.5754ZM9.00166 7.2915C6.59481 7.2915 4.9401 9.80474 5.90324 12.0899C5.96492 12.2362 6.10217 12.3236 6.24589 12.3236H6.94887C7.48002 12.3236 7.93216 12.6903 8.05675 13.1971L8.96175 16.878C9.08333 17.3725 9.51593 17.7082 9.99977 17.7082C10.4836 17.7082 10.9162 17.3725 11.0378 16.878L11.9428 13.1971C12.0674 12.6903 12.5195 12.3236 13.0507 12.3236H13.7537C13.8974 12.3236 14.0346 12.2362 14.0963 12.0899C15.0594 9.80474 13.4047 7.2915 10.9979 7.2915H9.00166Z" fill="#141B34" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.2915C8.96447 2.2915 8.125 3.13097 8.125 4.1665C8.125 5.20204 8.96447 6.0415 10 6.0415C11.0355 6.0415 11.875 5.20204 11.875 4.1665C11.875 3.13097 11.0355 2.2915 10 2.2915ZM6.875 4.1665C6.875 2.44061 8.27411 1.0415 10 1.0415C11.7259 1.0415 13.125 2.44061 13.125 4.1665C13.125 5.89239 11.7259 7.2915 10 7.2915C8.27411 7.2915 6.875 5.89239 6.875 4.1665Z" fill="#141B34" />
                            </svg>
                            <?= 'test'; ?>
                        </div>
                    <? endif; ?>
                    <? if (!is_null($arElement['PROPERTY_QUANTITY_HUMEN_VALUE']) || !is_null($arElement['PROPERTY_QUANTITY_CHILD_VALUE'])): ?>
                        <div class="room__features">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.75137 12.5754C3.45346 9.49595 5.66865 6.0415 9.00166 6.0415H10.9979C14.3309 6.0415 16.5461 9.49595 15.2482 12.5754C14.9949 13.1763 14.4095 13.5736 13.7537 13.5736H13.1374L12.2516 17.1764C11.9958 18.217 11.072 18.9582 9.99977 18.9582C8.92752 18.9582 8.00373 18.217 7.7479 17.1764L6.86209 13.5736H6.24589C5.59004 13.5736 5.00465 13.1763 4.75137 12.5754ZM9.00166 7.2915C6.59481 7.2915 4.9401 9.80474 5.90324 12.0899C5.96492 12.2362 6.10217 12.3236 6.24589 12.3236H6.94887C7.48002 12.3236 7.93216 12.6903 8.05675 13.1971L8.96175 16.878C9.08333 17.3725 9.51593 17.7082 9.99977 17.7082C10.4836 17.7082 10.9162 17.3725 11.0378 16.878L11.9428 13.1971C12.0674 12.6903 12.5195 12.3236 13.0507 12.3236H13.7537C13.8974 12.3236 14.0346 12.2362 14.0963 12.0899C15.0594 9.80474 13.4047 7.2915 10.9979 7.2915H9.00166Z" fill="#141B34" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 2.2915C8.96447 2.2915 8.125 3.13097 8.125 4.1665C8.125 5.20204 8.96447 6.0415 10 6.0415C11.0355 6.0415 11.875 5.20204 11.875 4.1665C11.875 3.13097 11.0355 2.2915 10 2.2915ZM6.875 4.1665C6.875 2.44061 8.27411 1.0415 10 1.0415C11.7259 1.0415 13.125 2.44061 13.125 4.1665C13.125 5.89239 11.7259 7.2915 10 7.2915C8.27411 7.2915 6.875 5.89239 6.875 4.1665Z" fill="#141B34" />
                            </svg>

                            <?= $arElement['PROPERTY_QUANTITY_HUMEN_VALUE'] . ' ' . $guestsDeclension->get($arElement['PROPERTY_QUANTITY_HUMEN_VALUE']) ?>, <?= !is_null($arElement['PROPERTY_QUANTITY_CHILD_VALUE']) ? $arElement['PROPERTY_QUANTITY_CHILD_VALUE'] . ' ' . $childrenDeclension->get($arElement['PROPERTY_QUANTITY_CHILD_VALUE']) : "бзе детей" ?>
                        </div>
                    <? endif ?>
                    <? if (!empty($arElement["PROPERTY_BEDS_VALUE"])): ?>
                        <div class="room__features">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.04199 14.5835C1.04199 14.2383 1.32181 13.9585 1.66699 13.9585L18.3337 13.9585C18.6788 13.9585 18.9587 14.2383 18.9587 14.5835C18.9587 14.9287 18.6788 15.2085 18.3337 15.2085L1.66699 15.2085C1.32181 15.2085 1.04199 14.9287 1.04199 14.5835Z" fill="#141B34" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.95699 9.375L15.0437 9.375C15.7924 9.37498 16.4167 9.37496 16.9124 9.44159C17.4356 9.51193 17.9079 9.66666 18.2874 10.0462C18.667 10.4258 18.8217 10.8981 18.8921 11.4213C18.9587 11.9169 18.9587 12.5413 18.9587 13.29L18.9587 17.5C18.9587 17.8452 18.6788 18.125 18.3337 18.125C17.9885 18.125 17.7087 17.8452 17.7087 17.5L17.7087 13.3333C17.7087 12.53 17.7073 11.9904 17.6532 11.5878C17.6015 11.2033 17.5122 11.0387 17.4036 10.9301C17.295 10.8215 17.1304 10.7321 16.7458 10.6804C16.3433 10.6263 15.8037 10.625 15.0003 10.625L5.00033 10.625C4.19698 10.625 3.65735 10.6263 3.25482 10.6804C2.87027 10.7321 2.70569 10.8215 2.59709 10.9301C2.48849 11.0387 2.39914 11.2033 2.34744 11.5878C2.29332 11.9904 2.29199 12.53 2.29199 13.3333L2.29199 17.5C2.29199 17.8452 2.01217 18.125 1.66699 18.125C1.32182 18.125 1.04199 17.8452 1.04199 17.5L1.04199 13.29C1.04197 12.5413 1.04195 11.9169 1.10859 11.4213C1.17892 10.8981 1.33365 10.4258 1.71321 10.0462C2.09277 9.66666 2.5651 9.51193 3.08826 9.44159C3.58391 9.37496 4.20826 9.37498 4.95699 9.375Z" fill="#141B34" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.62506 8.49478C5.62502 8.5 5.625 8.50545 5.625 8.51113L5.625 10C5.625 10.3452 5.34518 10.625 5 10.625C4.65482 10.625 4.375 10.3452 4.375 10L4.375 8.51113C4.375 8.34805 4.38114 8.10535 4.4996 7.87706C4.6318 7.62227 4.84491 7.4895 5.00705 7.40649C5.57701 7.11468 6.2925 6.875 7.08333 6.875C7.87417 6.875 8.58966 7.11468 9.15962 7.40649C9.32175 7.4895 9.53487 7.62227 9.66707 7.87706C9.78553 8.10535 9.79167 8.34805 9.79167 8.51113V10C9.79167 10.3452 9.51185 10.625 9.16667 10.625C8.82149 10.625 8.54167 10.3452 8.54167 10V8.51113C8.54167 8.50545 8.54165 8.5 8.54161 8.49478C8.10552 8.27855 7.60423 8.125 7.08333 8.125C6.56244 8.125 6.06114 8.27855 5.62506 8.49478Z" fill="#141B34" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.459 8.49478C11.459 8.5 11.459 8.50545 11.459 8.51113V10C11.459 10.3452 11.1792 10.625 10.834 10.625C10.4888 10.625 10.209 10.3452 10.209 10V8.51113C10.209 8.34805 10.2151 8.10535 10.3336 7.87706C10.4658 7.62227 10.6789 7.4895 10.841 7.40649C11.411 7.11468 12.1265 6.875 12.9173 6.875C13.7082 6.875 14.4236 7.11468 14.9936 7.40649C15.1557 7.4895 15.3689 7.62227 15.5011 7.87706C15.6195 8.10535 15.6257 8.34805 15.6257 8.51113V10C15.6257 10.3452 15.3458 10.625 15.0007 10.625C14.6555 10.625 14.3757 10.3452 14.3757 10V8.51113C14.3757 8.50545 14.3756 8.5 14.3756 8.49478C13.9395 8.27855 13.4382 8.125 12.9173 8.125C12.3964 8.125 11.8951 8.27855 11.459 8.49478Z" fill="#141B34" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0003 3.125C7.69917 3.125 5.57341 3.75972 3.83328 4.83415C3.33922 5.13921 3.26513 5.20174 3.19907 5.31403C3.14362 5.40828 3.12527 5.49888 3.12527 6.13381L3.12527 10C3.12527 10.3452 2.84545 10.625 2.50027 10.625C2.15509 10.625 1.87527 10.3452 1.87527 10L1.87527 6.13381C1.87527 6.10217 1.8752 6.07066 1.87514 6.03926C1.87409 5.55678 1.8731 5.10273 2.1217 4.68018L2.66038 4.99711L2.1217 4.68018C2.36196 4.27179 2.71665 4.05357 3.10826 3.81264C3.13091 3.7987 3.15369 3.78469 3.17657 3.77056C5.11571 2.57324 7.46916 1.875 10.0003 1.875C12.5314 1.875 14.8848 2.57324 16.824 3.77056C16.8469 3.78469 16.8696 3.7987 16.8923 3.81264C17.2839 4.05357 17.6386 4.27179 17.8789 4.68018C18.1274 5.10273 18.1265 5.55678 18.1254 6.03926C18.1253 6.07066 18.1253 6.10217 18.1253 6.13381L18.1253 10C18.1253 10.3452 17.8455 10.625 17.5003 10.625C17.1551 10.625 16.8753 10.3452 16.8753 10V6.13381C16.8753 5.49888 16.8569 5.40828 16.8015 5.31403C16.7354 5.20174 16.6613 5.13921 16.1673 4.83415C14.4271 3.75972 12.3014 3.125 10.0003 3.125Z" fill="#141B34" />
                            </svg>
                            <?= $arElement["PROPERTY_BEDS_VALUE"] . ' ' . $bedsDeclension->get($arElement["PROPERTY_BEDS_VALUE"]) ?>
                        </div>
                    <? endif; ?>
                    <? if (!is_null($arElement['PROPERTY_WITH_PETS_VALUE']) && $arElement['PROPERTY_WITH_PETS_VALUE'] === "Y") { ?>
                        <div class="room__features">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.0783 6.73408C16.489 6.6994 16.9388 6.81365 17.26 7.0783C17.6796 7.42405 17.8902 7.93488 17.9368 8.46869C17.999 9.1818 17.7269 9.92307 17.272 10.4673C16.8783 10.9383 16.3122 11.3411 15.6852 11.3974L15.6689 11.3997C15.2211 11.4579 14.8056 11.3243 14.4532 11.0474C14.0974 10.768 13.8637 10.2919 13.8114 9.84627C13.719 9.05996 13.9614 8.31129 14.4504 7.69356C14.852 7.18631 15.4274 6.8102 16.0783 6.73408Z" fill="black" />
                                <path d="M3.76666 6.73486C4.33871 6.75461 4.85266 6.98449 5.26748 7.37863C5.81668 7.90045 6.16543 8.67266 6.18123 9.43256C6.19229 9.96441 6.06635 10.5317 5.68445 10.9259C5.44852 11.1694 5.14127 11.3371 4.8061 11.3942C4.69219 11.4135 4.57651 11.4145 4.46127 11.4163C4.34637 11.41 4.23416 11.402 4.12129 11.3781C3.71096 11.2914 3.33276 11.0712 3.02106 10.7959C2.45014 10.2916 2.08543 9.53889 2.04287 8.77859C2.0124 8.23451 2.1636 7.66867 2.53529 7.25875C2.86014 6.90049 3.29604 6.7575 3.76666 6.73486Z" fill="black" />
                                <path d="M7.23097 2.81811C7.69628 2.78835 8.15477 3.00952 8.49698 3.30966C9.097 3.83593 9.45569 4.65901 9.50024 5.45087C9.53844 6.13017 9.33649 6.81059 8.87407 7.31767C8.56679 7.6546 8.16737 7.85595 7.71057 7.87843C7.23003 7.90276 6.78227 7.69733 6.4228 7.38977C5.82263 6.87624 5.50028 6.06485 5.44325 5.28974C5.39556 4.64151 5.56805 3.95249 5.9963 3.45343C6.31889 3.07751 6.73768 2.85587 7.23097 2.81811Z" fill="black" />
                                <path d="M12.5465 2.81814C12.9555 2.78245 13.3691 2.90378 13.6952 3.15409C14.1953 3.53808 14.4702 4.1198 14.5514 4.73704C14.6577 5.5455 14.4248 6.3739 13.9288 7.01804C13.5929 7.45429 13.1237 7.7778 12.5713 7.85151C12.1253 7.89903 11.7053 7.78026 11.3483 7.50831C10.8674 7.14194 10.6086 6.56173 10.5317 5.97349C10.4265 5.16825 10.6743 4.29138 11.1702 3.65054C11.5186 3.20034 11.9762 2.89085 12.5465 2.81814Z" fill="black" />
                                <path d="M9.73585 8.39486C10.3852 8.32212 11.1575 8.54968 11.6915 8.91794C12.1896 9.26142 12.5765 9.72031 12.842 10.2635C12.9355 10.4549 13.0162 10.6528 13.1142 10.842C13.214 11.0349 13.3387 11.2259 13.4718 11.3974C14.1384 12.2563 15.3137 12.7368 15.5235 13.9019C15.6106 14.3856 15.503 14.8623 15.3256 15.3121C15.2181 15.5845 15.0829 15.8521 14.9137 16.0916C14.5688 16.5796 14.0571 17.0413 13.4527 17.1544C13.2892 17.1849 13.1081 17.1683 12.9449 17.141C12.5753 17.0791 12.206 16.9374 11.8702 16.7739C11.3614 16.5261 10.8798 16.2298 10.3108 16.1436C9.93774 16.0991 9.58503 16.1411 9.22905 16.2608C9.01642 16.3323 8.81462 16.435 8.61397 16.5345C8.08188 16.7982 7.56222 17.0733 6.96343 17.1429L6.91515 17.1483C6.42425 17.1895 5.98438 16.9862 5.61472 16.6764C4.98138 16.1455 4.54569 15.3331 4.47567 14.5082C4.44884 14.1922 4.48159 13.8702 4.59114 13.5715C4.73005 13.1926 4.98956 12.8649 5.27011 12.5801C5.46823 12.379 5.68644 12.1951 5.89503 12.0048C6.1353 11.7855 6.37394 11.5625 6.56495 11.2974C6.823 10.9395 6.99419 10.5434 7.20456 10.1583C7.34524 9.90085 7.50761 9.65652 7.69835 9.4333C8.07276 8.99507 8.6278 8.64117 9.18235 8.4872C9.36466 8.4366 9.54772 8.41048 9.73585 8.39486Z" fill="black" />
                            </svg>
                            Можно с животными
                        </div>
                    <? } ?>

                    <a class="room__features-more" href="#" elementId="<?= $arElement['ID'] ?>" data-room-more="<?= $checksum ?>">Подробнее о номере</a>
                </div>
            </div>
        </div>
        <div class="room__order">
            <div class="room__left">
                <? if (empty($dateFrom) && empty($dateTo)) { ?>
                    <div class="no-date">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <path d="M24 0C10.7447 0 0 10.7449 0 23.9994C0 37.2539 10.7447 48 24 48C37.2553 48 48 37.2551 48 23.9994C48 10.7438 37.2542 0 24 0Z" fill="#1B2E50" />
                            <path d="M30.3323 12.7465V10.6665C30.3323 10.1198 29.879 9.6665 29.3323 9.6665C28.7857 9.6665 28.3323 10.1198 28.3323 10.6665V12.6665H19.6657V10.6665C19.6657 10.1198 19.2123 9.6665 18.6657 9.6665C18.119 9.6665 17.6657 10.1198 17.6657 10.6665V12.7465C14.0657 13.0798 12.319 15.2265 12.0523 18.4132C12.0257 18.7998 12.3457 19.1198 12.719 19.1198H35.279C35.6657 19.1198 35.9857 18.7865 35.9457 18.4132C35.679 15.2265 33.9323 13.0798 30.3323 12.7465Z" fill="#E39250" />
                            <path d="M34.6667 21.1201H13.3333C12.6 21.1201 12 21.7201 12 22.4535V30.6668C12 34.6668 14 37.3335 18.6667 37.3335H25.24C26.16 37.3335 26.8 36.4401 26.5067 35.5735C26.24 34.8001 26.0133 33.9468 26.0133 33.3335C26.0133 29.2935 29.3067 26.0001 33.3467 26.0001C33.7333 26.0001 34.12 26.0268 34.4933 26.0935C35.2933 26.2134 36.0133 25.5868 36.0133 24.7868V22.4668C36 21.7201 35.4 21.1201 34.6667 21.1201ZM20.28 31.6134C20.0267 31.8534 19.68 32.0001 19.3333 32.0001C19.16 32.0001 18.9867 31.9601 18.8267 31.8935C18.6667 31.8268 18.52 31.7334 18.3867 31.6134C18.1467 31.3601 18 31.0268 18 30.6668C18 30.4935 18.04 30.3201 18.1067 30.1601C18.1733 29.9868 18.2667 29.8535 18.3867 29.7201C18.52 29.6001 18.6667 29.5068 18.8267 29.4401C19.3067 29.2268 19.9067 29.3468 20.28 29.7201C20.4 29.8535 20.4933 29.9868 20.56 30.1601C20.6267 30.3201 20.6667 30.4935 20.6667 30.6668C20.6667 31.0268 20.52 31.3601 20.28 31.6134ZM20.28 26.9468C20.0267 27.1868 19.68 27.3335 19.3333 27.3335C19.16 27.3335 18.9867 27.3068 18.8267 27.2268C18.6667 27.1601 18.52 27.0668 18.3867 26.9468C18.1467 26.6935 18 26.3468 18 26.0001C18 25.8268 18.04 25.6535 18.1067 25.4935C18.1733 25.3335 18.2667 25.1868 18.3867 25.0535C18.52 24.9335 18.6667 24.8401 18.8267 24.7735C19.3067 24.5735 19.9067 24.6801 20.28 25.0535C20.4 25.1868 20.4933 25.3335 20.56 25.4935C20.6267 25.6535 20.6667 25.8268 20.6667 26.0001C20.6667 26.3468 20.52 26.6935 20.28 26.9468ZM25.2267 26.5068C25.16 26.6668 25.0667 26.8135 24.9467 26.9468C24.8133 27.0668 24.6667 27.1601 24.5067 27.2268C24.3467 27.3068 24.1733 27.3335 24 27.3335C23.6533 27.3335 23.3067 27.1868 23.0533 26.9468C22.9333 26.8135 22.84 26.6668 22.7733 26.5068C22.7067 26.3468 22.6667 26.1735 22.6667 26.0001C22.6667 25.6535 22.8133 25.3068 23.0533 25.0535C23.4267 24.6801 24.0133 24.5601 24.5067 24.7735C24.6667 24.8401 24.8133 24.9335 24.9467 25.0535C25.1867 25.3068 25.3333 25.6535 25.3333 26.0001C25.3333 26.1735 25.3067 26.3468 25.2267 26.5068Z" fill="#E39250" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M37.6402 30.3598C37.7866 30.5063 37.7866 30.7437 37.6402 30.8902L30.8902 37.6402C30.7437 37.7866 30.5063 37.7866 30.3598 37.6402C30.2134 37.4937 30.2134 37.2563 30.3598 37.1098L37.1098 30.3598C37.2563 30.2134 37.4937 30.2134 37.6402 30.3598Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M30.3598 30.3598C30.5063 30.2134 30.7437 30.2134 30.8902 30.3598L37.6402 37.1098C37.7866 37.2563 37.7866 37.4937 37.6402 37.6402C37.4937 37.7866 37.2563 37.7866 37.1098 37.6402L30.3598 30.8902C30.2134 30.7437 30.2134 30.5063 30.3598 30.3598Z" fill="white" />
                        </svg>
                        Чтобы увидеть цены, выберите даты
                    </div>
                <? } else { ?>
                    <div class="no-date">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <path d="M24 0C10.7447 0 0 10.7449 0 23.9994C0 37.2539 10.7447 48 24 48C37.2553 48 48 37.2551 48 23.9994C48 10.7438 37.2542 0 24 0Z" fill="#1B2E50" />
                            <path d="M35.8309 20.0276C35.8309 30.9667 23.9999 39.887 23.9999 39.887C23.9999 39.887 12.1689 31.0317 12.1689 20.0276C12.1689 16.8675 13.4154 13.8368 15.6342 11.6023C17.8529 9.36769 20.8621 8.1123 23.9999 8.1123C27.1377 8.1123 30.147 9.36769 32.3657 11.6023C34.5844 13.8368 35.8309 16.8675 35.8309 20.0276Z" fill="#E39250" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M29.4602 14.7898C29.6799 15.0094 29.6799 15.3656 29.4602 15.5852L19.3352 25.7102C19.1156 25.9299 18.7594 25.9299 18.5398 25.7102C18.3201 25.4906 18.3201 25.1344 18.5398 24.9148L28.6648 14.7898C28.8844 14.5701 29.2406 14.5701 29.4602 14.7898Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.5398 14.7898C18.7594 14.5701 19.1156 14.5701 19.3352 14.7898L29.4602 24.9148C29.6799 25.1344 29.6799 25.4906 29.4602 25.7102C29.2406 25.9299 28.8844 25.9299 28.6648 25.7102L18.5398 15.5852C18.3201 15.3656 18.3201 15.0094 18.5398 14.7898Z" fill="white" />
                        </svg>
                        <div class="no-date__text">
                            На выбранные даты нет свободных мест
                            <? if ($searchError) { ?>
                                <span><?= $searchError ?></span>
                            <? } ?>
                        </div>
                    </div>
                <? } ?>
            </div>
            <a class="button button_primary" data-scroll-to href="<?= $isMobile ? '#fake-filter_catalog' : '#form-object-filter' ?>"><?= empty($dateFrom) && empty($dateTo) ? 'Выбрать даты' : 'Изменить даты' ?></a>
        </div>
    </div>
<? endforeach;