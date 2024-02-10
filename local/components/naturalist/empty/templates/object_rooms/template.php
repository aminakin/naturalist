<?php

use Naturalist\Users;

global $isAuthorized;

foreach ($arParams['VARS'] as $key => $value) {
    ${$key} = $value;
}

?>
<div class="rooms" data-object-container>
    <div class="rooms__heading h3">Выберите номер <span><?=$daysRange?>, <?=$guests?> <?=$guestsDeclension->get($guests)?><?if($children > 0):?>, <?=$children?> <?=$childrenDeclension->get($children)?><?endif;?></span></div>

    <div class="rooms__list">
        <?if($arSection["UF_EXTERNAL_SERVICE"] == "bnovo"):?>
            <?foreach($arElements as $arElement):
                $arElementsTariffs[$arElement['ID']] = $arElement;
            endforeach;
            $arParentView = [];
            ?>
            <?foreach($arExternalInfo as $idNumber => $arTariffs):
                ?>
                <?foreach($arTariffs as $keyTariff => $arTariff):
                if (empty($arTariff['prices']) || empty($arElementsTariffs[$idNumber])) {
                    continue;
                }

                $arElement = $arElementsTariffs[$idNumber];

                if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] != 0 && !in_array($arElement["PROPERTY_PARENT_ID_VALUE"].'-'.$arTariff['tariffId'], $arParentView)) {
                    $arParentView[] = $arElement["PROPERTY_PARENT_ID_VALUE"].'-'.$arTariff['tariffId'];
                } elseif((int)$arElement["PROPERTY_PARENT_ID_VALUE"] == 0 && !in_array($arElement["PROPERTY_EXTERNAL_ID_VALUE"].'-'.$arTariff['tariffId'], $arParentView)) {
                    $arParentView[] = $arElement["PROPERTY_EXTERNAL_ID_VALUE"].'-'.$arTariff['tariffId'];
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
                    <?if($arElement["PICTURES"]):?>
                        <div class="room__images">
                            <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $arElement["FULL_GALLERY_ROOM"];?>]">
                                <div class="swiper-wrapper">
                                    <? $keyPhoto = 1; ?>
                                    <? $keyPhotoRoom = 0; ?>
                                    <?foreach($arElement["PICTURES"] as $arPhoto):?>
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
                                        <div class="swiper-slide" data-fullgallery-item="<?= $keyPhotoRoom; ?>">
                                            <img class="" loading="lazy" alt="<?= $alt; ?>" title="<?= $title; ?>" src="<?=$arPhoto["src"]?>">
                                        </div>
                                        <? $keyPhoto++; ?>
                                        <? $keyPhotoRoom++; ?>
                                    <?endforeach;?>
                                </div>

                                <?if(count($arElement["PICTURES"]) > 1):?>
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
                                <?endif;?>
                            </div>
                        </div>
                    <?endif;?>

                    <div class="room__content">
                        <div class="room__description">
                            <div class="h3"><?=$arElement["NAME"] . ' ' . ($arTariff['value']['PROPERTY_NAME_DETAIL_VALUE'] ?? $arTariff['value']['NAME'])?></div>
                            <?if(!empty($arElement["PROPERTY_SQUARE_VALUE"])):?>
                                <div class="room__features">Площадь: <?=$arElement["PROPERTY_SQUARE_VALUE"]?></div>
                            <?endif;?>
                            <?
                            $text = plural_form($guests, array('взрослый на основном месте', 'взрослых на основных местах', 'взрослых на основных местах'));
                            if($children > 0){
                                if(!empty($arSection['UF_MIN_AGE'])) {
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
                                    if(!empty($miniChildren)){
                                        $text .= ', '. $miniChildren. ' ' .$childrenDeclension->get($miniChildren).' без места';
                                    }

                                    if(!empty($bigChildren)) {
                                        $text .= ', '. plural_form($bigChildren, array('ребенок на основном месте', 'детей на основных местах', 'детей на основных местах'));
                                    }
                                } else{
                                    $text .= ', '. plural_form($children, array('ребенок на основном месте', 'детей на основных местах', 'детей на основных местах'));
                                }
                            }
                            $text .= '.';
                            ?>
                            <div class="room__features"><?=$text; ?></div>
                            <?if(!empty($arElement["DETAIL_TEXT"])):?>
                                <div class="room__features"><?=$arElement["DETAIL_TEXT"]?></div>
                            <?endif;?>
                            <?if($arElement["PROPERTY_FEATURES_VALUE"]):?>
                                <div class="room__features" data-room-features>
                                    <ul class="list">
                                        <? $arSlicedFeatures = array_slice($arElement["PROPERTY_FEATURES_VALUE"], 0, 6);
                                        foreach($arSlicedFeatures as $key => $featureId):?>
                                            <li class="list__item"><?=$arHLRoomFeatures[$featureId]["UF_NAME"]?></li>
                                        <?endforeach;?>
                                    </ul>
                                </div>
                            <?endif;?>
                            <div class="room__features">
                                <a class="room__features-more" href="#" data-room-more="<?=$arElement['ID'].'-'.$arTariff['tariffId']?>">Подробнее о номере</a>
                            </div>
                        </div>
                        <?if($arTariff['price']):?>
                            <?$elementOldPrice = 0;?>
                            <?if ($arElement['DISCOUNT_DATA']) {                                
                                if ($arElement['DISCOUNT_DATA']['VALUE_TYPE'] == 'P') {
                                    $elementPrice = $arTariff['price'] * (100 - $arElement['DISCOUNT_DATA']['VALUE']) / 100;
                                } else {
                                    $elementPrice = $arTariff['price'] - $arElement['DISCOUNT_DATA']['VALUE'];
                                }                                        
                                $elementOldPrice = $arTariff['price'];
                            } else {
                                $elementPrice = $arTariff['price'];
                            }?>
                            <div class="room__order">

                                <?php if ($USER->IsAdmin()):?>
                                    <?php if (
                                        $elementPrice > Users::getInnerScore()
                                        && Users::getInnerScore() !== 0
                                        && $isAuthorized
                                    ):?>
                                        <div class="room__price_cert_price">
                                            <div class="room__price_cert_price-item">
                                                <span>Доплата</span>
                                                <span>
                                                    <?=number_format($elementPrice - Users::getInnerScore(), 0, '.', ' ')?> ₽
                                                </span>
                                            </div>
                                        </div>
                                    <? endif; ?>
                                <? endif; ?>
                             
                                <div class="room__price">
                                    <?if ($elementOldPrice) {?>
                                        <span class="room__old-price"><?= number_format($elementOldPrice, 0, '.', ' ') ?> ₽</span>
                                    <?}?>
                                    <div class="room__price-per-night">
                                        <span class="room__final-price"><?= number_format($elementPrice, 0, '.', ' ') ?> ₽</span>
                                        <span class="room__nights">за <?=$daysCount?> <?=$daysDeclension->get($daysCount)?></span>
                                    </div>
                                    <?if (Bitrix\Main\Engine\CurrentUser::get()->isAdmin()) {?>
                                        <div class="split-wrap">
                                            <yandex-pay-badge
                                                merchant-id="d82873ad-61ce-4050-b05e-1f4599f0bb7b"
                                                type="bnpl"
                                                amount="<?=$elementPrice?>"
                                                size="l"
                                                variant="detailed"
                                                theme="light"
                                                color="primary"
                                            />
                                        </div>
                                    <?}?>
                                </div>

                                <a class="button button_primary"
                                   onclick="VK.Goal('customize_product')"
                                   data-add-basket
                                   data-id="<?=$arElement["ID"]?>"
                                   data-price="<?=$arTariff['price']?>"
                                   data-guests="<?=$guests?>"
                                   data-children-age="<?=$_GET['childrenAge']?>"
                                   data-date-from="<?=$dateFrom?>"
                                   data-date-to="<?=$dateTo?>"
                                   data-external-id="<?=$arElement["PROPERTY_EXTERNAL_ID_VALUE"]?>"
                                   data-external-service="<?=$arSection["UF_EXTERNAL_SERVICE"]?>"
                                   data-tariff-id='<?=$arTariff['tariffId']?>'
                                   data-category-id="<?=$arTariff['categoryId']?>"
                                   data-prices='<?=serialize($arTariff['prices'])?>'
                                   data-cancel-amount="<?=$arTariff['cancelAmount']?>"
                                   data-people="<?=$text?>"
                                   data-room-title="<?=$arElement["NAME"] . ' ' . ($arTariff['value']['PROPERTY_NAME_DETAIL_VALUE'] ?? $arTariff['value']['NAME'])?>"
                                   data-room-photo="<?=$arElement["PICTURES"][array_key_first($arElement["PICTURES"])]['src']?>"
                                   href="#"
                                >Забронировать</a>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            <?endforeach;?>
            <?endforeach;?>
        <?else:?>
            <?foreach($arElements as $arElement):?>                
                <?if($arExternalInfo[$arElement["ID"]]):?>
                    <?foreach($arExternalInfo[$arElement["ID"]] as $checksum => $arExternalItem):?>                        
                        <div class="room">
                            <?if($arElement["PICTURES"]):?>
                                <div class="room__images">
                                    <div class="swiper slider-gallery" data-slider-object="data-slider-object" data-fullgallery="[<?= $arElement["FULL_GALLERY_ROOM"];?>]">
                                        <div class="swiper-wrapper">
                                            <? $keyPhoto = 1; ?>
                                            <? $keyPhotoRoom = 0; ?>
                                            <?foreach($arElement["PICTURES"] as $arPhoto):?>
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
                                                <div class="swiper-slide" data-fullgallery-item="<?= $keyPhotoRoom; ?>">
                                                    <img class="" loading="lazy" alt="<?= $alt; ?>" title="<?= $title; ?>" src="<?=$arPhoto["src"]?>">
                                                </div>
                                                <? $keyPhoto++; ?>
                                                <? $keyPhotoRoom++; ?>
                                            <?endforeach;?>
                                        </div>

                                        <?if(count($arElement["PICTURES"]) > 1):?>
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
                                        <?endif;?>
                                    </div>
                                </div>
                            <?endif;?>

                            <div class="room__content">
                                <div class="room__description">
                                    <div class="h3"><?=$arElement["NAME"]?></div>
                                    <?if(!empty($arElement["PROPERTY_SQUARE_VALUE"])):?>
                                        <div class="room__features">Площадь: <?=$arElement["PROPERTY_SQUARE_VALUE"]?> м²</div>
                                    <?endif;?>
                                    <?if(!empty($arExternalItem['fullPlacementsName'])):?>
                                        <div class="room__features"><?=$arExternalItem['fullPlacementsName']?></div>
                                    <?endif;?>
                                    <?if(!empty($arElement["DETAIL_TEXT"])):?>
                                        <div class="room__features"><?=htmlspecialcharsBack($arElement["DETAIL_TEXT"])?></div>
                                    <?endif;?>
                                    <?if($arElement["PROPERTY_FEATURES_VALUE"]):?>
                                        <?
                                        $arSlicedFeatures = array_slice($arElement["PROPERTY_FEATURES_VALUE"], 0, 6);
                                        ?>
                                        <div class="room__features">
                                            <ul class="list">
                                                <?foreach($arSlicedFeatures as $featureId):?>
                                                    <li class="list__item"><?=$arHLRoomFeatures[$featureId]["UF_NAME"]?></li>
                                                <?endforeach;?>
                                            </ul>
                                        </div>
                                    <?endif;?>
                                    <div class="room__features">
                                        <a class="room__features-more" href="#" data-room-more="<?=$checksum?>">Подробнее о номере</a>
                                    </div>
                                </div>

                                <?if($arExternalItem['price']):?>
                                    <?$elementOldPrice = 0;?>
                                    <?if ($arElement['DISCOUNT_DATA']) {                                        
                                        if ($arElement['DISCOUNT_DATA']['VALUE_TYPE'] == 'P') {
                                            $elementPrice = $arExternalItem['price'] * (100 - $arElement['DISCOUNT_DATA']['VALUE']) / 100;
                                        } else {
                                            $elementPrice = $arExternalItem['price'] - $arElement['DISCOUNT_DATA']['VALUE'];
                                        }                                        
                                        $elementOldPrice = $arExternalItem['price'];
                                    } else {
                                        $elementPrice = $arExternalItem['price'];
                                    }?>                                    
                                    <div class="room__order">

                                        <?php if ($USER->IsAdmin()):?>
                                            <?php if (
                                                $elementPrice > Users::getInnerScore()
                                                && Users::getInnerScore() !== 0
                                                && $isAuthorized
                                            ):?>
                                                <div class="room__price_cert_price">
                                                    <div class="room__price_cert_price-item">
                                                        <span>Доплата</span>
                                                        <span>
                                                            <?=number_format($elementPrice - Users::getInnerScore(), 0, '.', ' ')?> ₽
                                                        </span>
                                                    </div>
                                                </div>
                                            <? endif; ?>
                                        <? endif; ?>

                                        <div class="room__price">
                                            <?if ($elementOldPrice) {?>
                                                <span class="room__old-price"><?= number_format($elementOldPrice, 0, '.', ' ') ?> ₽</span>
                                            <?}?>
                                            <div class="room__price-per-night">
                                                <span class="room__final-price"><?= number_format($elementPrice, 0, '.', ' ') ?> ₽</span>
                                                <span class="room__nights">/ за <?=$daysCount?> <?=$daysDeclension->get($daysCount)?></span>
                                            </div>
                                            <?if (Bitrix\Main\Engine\CurrentUser::get()->isAdmin()) {?>
                                                <div class="split-wrap">
                                                    <yandex-pay-badge
                                                        merchant-id="d82873ad-61ce-4050-b05e-1f4599f0bb7b"
                                                        type="bnpl"
                                                        amount="<?=$elementPrice?>"
                                                        size="l"
                                                        variant="simple"
                                                        theme="light"
                                                        color="primary"
                                                    />
                                                </div>     
                                            <?}?>                                      
                                        </div>

                                        <a class="button button_primary"
                                           onclick="VK.Goal('customize_product')"
                                           data-add-basket
                                           data-id="<?=$arElement["ID"]?>"
                                           data-price="<?=$arExternalItem['price']?>"
                                           data-guests="<?=$guests?>"
                                           data-children-age="<?=$_GET['childrenAge']?>"
                                           data-date-from="<?=$dateFrom?>"
                                           data-date-to="<?=$dateTo?>"
                                           data-external-id="<?=$arElement["PROPERTY_EXTERNAL_ID_VALUE"]?>"
                                           data-external-service="<?=$arSection["UF_EXTERNAL_SERVICE"]?>"
                                           data-category-id="<?=$arElement["PROPERTY_EXTERNAL_CATEGORY_ID_VALUE"]?>"
                                           data-traveline-checksum="<?=$checksum?>"
                                           data-cancel-amount="<?=$arExternalItem['cancelAmount']?>"
                                           data-people="<?=$arExternalItem['fullPlacementsName']?>"
                                           data-room-title="<?=$arElement["NAME"]?>"                                           
                                           href="#"
                                        >Забронировать</a>
                                    </div>
                                <?endif;?>
                            </div>
                        </div>
                    <?endforeach;?>
                <?endif;?>
            <?endforeach;?>
        <?endif;?>

    </div>

    <?if($page < $pageCount):?>
        <div class="rooms__more">
            <a href="#" data-object-showmore data-page="<?=$page+1?>">Показать ещё</a>
        </div>
    <?endif;?>

    <?if($arSection["UF_EXTERNAL_SERVICE"] == "bnovo"):?>        
        <script>
            window.moreRooms = [
                <?foreach($arElementsJson as $arElement):
                $arElementsTariffs[$arElement['ID']] = $arElement;
            endforeach;?>
                <?foreach($arExternalInfo as $idNumber => $arTariffs):?>
                <?foreach($arTariffs as $keyTariff => $arTariff):
                $arElement = $arElementsTariffs[$idNumber];
                if ((int)$arElement["PROPERTY_PARENT_ID_VALUE"] > 0 && !empty($arElementsParent[$arElement['PROPERTY_PARENT_ID_VALUE']])) {
                    $arElement = $arElementsParent[$arElement['PROPERTY_PARENT_ID_VALUE']];
                    $arElement["ID"] = $arElementsTariffs[$idNumber]["ID"];
                    $arElement["PROPERTY_EXTERNAL_ID_VALUE"] = $arElementsTariffs[$idNumber]["PROPERTY_EXTERNAL_ID_VALUE"];
                }
                ?>
                {
                    "id": "<?=$arElement['ID'].'-'.$arTariff["tariffId"]?>",
                    "title": "<?=addslashes($arElement["NAME"]) . ' ' .  ($arTariff['value']['PROPERTY_NAME_DETAIL_VALUE'] ?? $arTariff['value']['NAME'])?>",
                    "footnote": "<?=!empty($arElement["PROPERTY_SQUARE_VALUE"]) ? "Площадь: ".$arElement["PROPERTY_SQUARE_VALUE"]." м² <br>".$arExternalItem['fullPlacementsName'] : $arExternalItem['fullPlacementsName']?>",
                    "text": `<?=$arElement["DETAIL_TEXT"] ?>`,
                    "furnishings": [
                        <?foreach($arElement["PROPERTY_FEATURES_VALUE"] as $featureId):?>
                        `<?=$arHLRoomFeatures[$featureId]["UF_NAME"]?>`,
                        <?endforeach;?>
                    ],
                    "services": [
                        <?foreach($arExternalItem['includedServices'] as $arServiceItem):?>
                        <?=$arServicesTraveline[$arServiceItem["id"]]["NAME"] ? "`".$arServicesTraveline[$arServiceItem["id"]]["NAME"]."`," : "" ?>
                        <?endforeach;?>
                    ],
                    "reservCancel": [
                        <?if(!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '2'):?>
                        <?=!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']) ? '`'.$arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'].'`,' : ''?>
                        "Штраф за отмену бронирования — <?=$arTariff['price']*($arTariff['value']['PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE']/100)?> ₽"

                        <?elseif(!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '5'):?>
                        <?=!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']) ? '`'.$arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'].'`,' : ''?>
                        "Штраф за отмену бронирования — <?=$arTariff['price']?> ₽"

                        <?elseif(!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE']) && $arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'] == '4'):?>
                        <?=!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']) ? '`'.$arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'].'`,' : ''?>
                        "Штраф за отмену бронирования — <?=array_shift($arTariff['prices'])?> ₽"

                        <?elseif(!empty($arTariff['value']['PROPERTY_CANCELLATION_FINE_TYPE_VALUE'])):?>
                        <?=!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']) ? '`'.$arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'].'`,' : ''?>
                        "Штраф за отмену бронирования — <?=$arTariff['value']['PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE']?> ₽"

                        <?else:?>
                        <?=!empty($arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE']) ? '`'.$arTariff['value']['PROPERTY_CANCELLATION_RULES_VALUE'].'`,' : ''?>
                        "Бесплатная отмена бронирования"
                        <?endif;?>
                    ]
                },
                <?endforeach;?>
                <?endforeach;?>
            ];
        </script>
    <?else:?>
        <script>
            window.moreRooms = [
                <?foreach($arElementsJson as $arElement):?>
                <?if($arExternalInfo[$arElement["ID"]]):?>
                <?foreach($arExternalInfo[$arElement["ID"]] as $checksum => $arExternalItem):?>
                {
                    "id": "<?=$checksum?>",
                    "title": "<?=addslashes($arElement["NAME"])?>",
                    "footnote": "<?=!empty($arElement["PROPERTY_SQUARE_VALUE"]) ? "Площадь: ".$arElement["PROPERTY_SQUARE_VALUE"]." м² <br>".$arExternalItem['fullPlacementsName'] : $arExternalItem['fullPlacementsName']?>",
                    "text": `<?=$arElement["DETAIL_TEXT"] ?>`,
                    "furnishings": [
                        <?foreach($arElement["PROPERTY_FEATURES_VALUE"] as $featureId):?>
                        `<?=$arHLRoomFeatures[$featureId]["UF_NAME"]?>`,
                        <?endforeach;?>
                    ],
                    "services": [
                        <?foreach($arExternalItem['includedServices'] as $arServiceItem):?>
                        <?=$arServicesTraveline[$arServiceItem["id"]]["NAME"] ? "`".$arServicesTraveline[$arServiceItem["id"]]["NAME"]."`," : "" ?>
                        <?endforeach;?>
                    ],
                    "reservCancel": [
                        <?if($arExternalItem['cancelPossible'] && $arExternalItem['cancelAmount'] > 0):?>
                        "Бесплатная отмена до <?=$arExternalItem['cancelDate']?> (Московское время)",
                        "Далее штраф за отмену бронирования — <?=$arExternalItem['cancelAmount']?> ₽"
                        <?elseif($arExternalItem['cancelAmount'] > 0):?>
                        "Штраф за отмену бронирования — <?=$arExternalItem['cancelAmount']?> ₽"
                        <?else:?>
                        "Бесплатная отмена бронирования"
                        <?endif;?>
                    ]
                },
                <?endforeach;?>
                <?endif;?>
                <?endforeach;?>
            ];
        </script>
    <?endif;?>
</div>