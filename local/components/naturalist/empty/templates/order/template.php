<?php

use Bitrix\Main\Localization\Loc;
use Naturalist\Users;

global $isAuthorized;

foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>

<div class="reservation">
    <div class="reservation__content">
        <div class="reservation__heading">
            <div class="reservation__heading-title">
                <h1 class="h3"><?= $arSection["NAME"] ?></h1>
                <div class="area-info">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt>
                    <div>
                        <? if (isset($arHLTypes[$arSection["UF_TYPE"]])) : ?><span><?= $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] ?></span><? endif; ?>
                        <? if (!empty($arSection["UF_DISTANCE"])) : ?><span><?= $arSection["UF_DISTANCE"] ?></span><? endif; ?>
                        <? if (!empty($arSection["UF_ADDRESS"])) : ?><span><?= $arSection["UF_ADDRESS"] ?></span><? endif; ?>
                    </div>
                </div>                
            </div>

            <div class="reservation__heading-score">
                <div class="score">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                    <span><?= $avgRating ?></span>
                </div>
                <a href="<?= $arSection["SECTION_PAGE_URL"] ?>"><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?></a>
            </div>
        </div>

        <ul class="list list_summary_time">
            <li class="list__item">
                <div class="h3"><?=Loc::getMessage('ORDER_CHECKIN')?></div>
                <span><?=FormatDate("d F Y, D", strtotime($dateFrom))?></span>
                <span>с <?=$arSection["UF_TIME_FROM"]?></span>
            </li>
            <li class="list__item">
                <div class="h3"><?=Loc::getMessage('ORDER_CHECKOUT')?></div>
                <span><?=FormatDate("d F Y, D", strtotime($dateTo))?></span>
                <span>до <?=$arSection["UF_TIME_TO"]?></span>
            </li>
        </ul>

        <ul class="list list_icon">
            <li class="list__item">
                <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/5.svg" alt></div>
                <div class="list__item-title"><?=$arElement["NAME"]?></div>
            </li>
            <li class="list__item">
                <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/1.svg" alt></div>
                <div class="list__item-title"><?=FormatDate("d F", strtotime($dateFrom))?> - <?=FormatDate("d F", strtotime($dateTo))?>, <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?></div>
            </li>
            <li class="list__item">
                <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/3.svg" alt></div>
                <div class="list__item-title"><?= $guests ?> <?= $guestsDeclension->get($guests) ?></div>
            </li>
            <?if($children > 0):?>
                <li class="list__item">
                    <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/3.svg" alt></div>
                    <div class="list__item-title"><?= $children ?> <?= $childrenDeclension->get($children) ?></div>
                </li>
            <?endif;?>

            <?foreach($arSection["UF_FEATURES"] as $featureId):?>
                <?
                $arIcon = CFile::GetFileArray($arHLFeatures[$featureId]["UF_ICON"]);
                ?>
                <li class="list__item">
                    <div class="list__item-icon">
                        <img src="<?=$arIcon["SRC"]?>" alt="<?=$arHLFeatures[$featureId]["UF_NAME"]?>">
                    </div>
                    <div class="list__item-title"><?=$arHLFeatures[$featureId]["UF_NAME"]?></div>
                </li>
            <?endforeach;?>
        </ul>
    </div>

    <div class="reservation__image">
        <img class="lazy" data-src="<?=CFile::getPath($arResult['arElement']['PROPERTY_PHOTOS_VALUE'][0])?>" alt="<?=$arResult['arHLTypes'][$arSection['UF_TYPE']]['UF_NAME'] . ' ' . $arSection["NAME"]?>">
    </div>
</div>

<form class="form reservation-form form_validation" id="form-order" is_auth="true" <?/*is_auth="<?=$isAuthorized ? 'true' : 'false'*/?>>
    <div class="reservation-form__form">
        <div class="reservation-form__form-holder">            
            <p class="reservation-form__title"><?=Loc::getMessage('ORDER_FORM_TITLE')?></p>
            <div class="reservation-form__fields">
                <div class="reservation-form__fields-item">
                    <div class="form__row">
                        <div class="form__row-item">
                            <div class="field">
                                <input class="field__input" name="email" type="email" placeholder="<?=Loc::getMessage('ORDER_EMAIL_PLACEHOLDER')?>" value="<?=$arUser["EMAIL"]?>">                                
                            </div>
                            <span class="form__footnote"><?=Loc::getMessage('ORDER_CONFIRM_DATA_MAIL')?></span>
                        </div>
                        <div class="form__row-item">
                            <div class="field">
                                <input class="field__input" name="phone" type="tel" placeholder="<?=Loc::getMessage('ORDER_PHONE_PLACEHOLDER')?>" value="<?=$arUser["PERSONAL_PHONE"]?>">                                
                            </div>
                            <span class="form__footnote"><?=Loc::getMessage('ORDER_CONFIRM_DATA_PHONE')?></span>
                        </div>

                        <div class="field">
                            <input class="field__input" name="name" type="text" placeholder="<?=Loc::getMessage('ORDER_NAME_PLACEHOLDER')?>" value="<?=$arUser["NAME"]?>">
                        </div>
                        <div class="field">
                            <input class="field__input" name="surname" type="text" placeholder="<?=Loc::getMessage('ORDER_LAST_NAME_PLACEHOLDER')?>" value="<?=$arUser["LAST_NAME"]?>">
                        </div>

                        <input type="hidden" name="date_from" value="<?=$dateFrom?>" />
                        <input type="hidden" name="date_to" value="<?=$dateTo?>" />
                        <input type="hidden" name="guests" value="<?=$guests?>" />
                        <input type="hidden" name="childrenAge" value="<?=$childrenAge?>" />
                        <input type="hidden" name="service" value="<?=$arSection["UF_EXTERNAL_SERVICE"]?>" />
                        <input type="hidden" name="sectionId" value="<?=$arSection["ID"]?>" />
                        <input type="hidden" name="elementId" value="<?=$arElement["ID"]?>" />
                        <input type="hidden" name="externalId" value="<?=$arSection["UF_EXTERNAL_ID"]?>" />
                        <input type="hidden" name="externalElementId" value="<?=$arElement["PROPERTY_EXTERNAL_ID_VALUE"]?>" />
                        <input type="hidden" name="price" value="<?=$totalPrice?>" />
                        <input type="hidden" name="user_balance" value="<?=number_format(Users::getInnerScore(), 0, '.', '')?>">
                        <?if($arSection["UF_EXTERNAL_SERVICE"] == 1):?>
                            <input type="hidden" name="travelineCategoryId" value="<?=$arElement["PROPERTY_EXTERNAL_CATEGORY_ID_VALUE"]?>" />
                            <input type="hidden" name="travelineChecksum" value="<?=$checksum?>" />
                        <?else:?>
                            <input type="hidden" name="tariffId" value="<?=$tariffValue?>" />
                            <input type="hidden" name="priceOneNight" value="<?=$priceOneNight?>" />
                            <input type="hidden" name="categoryId" value="<?=$categoryId?>" />
                        <?endif;?>
                    </div>                    
                </div>

                <?/*for($i = 1; $i <= $guests; $i++):?>
                    <div class="reservation-form__fields-item" data-guest-row="<?=$i?>" data-check-disabled>
                        <div class="reservation-form__fields-label"><?=($i <= 5) ? $arGuestsNamesData[$i] : 'Дополнительный'?> гость</div>

                        <div class="form__row form__row_3" data-autocomplete>
                            <div class="field">
                                <input class="field__input" type="text" name="surname" value="<?=$arUser["UF_GUESTS_DATA"][$i]["surname"] ?? ''?>" data-autocomplete-field="last" placeholder="Фамилия">
                            </div>
                            <div class="field">
                                <input class="field__input" type="text" name="name" value="<?=$arUser["UF_GUESTS_DATA"][$i]["name"] ?? ''?>" data-autocomplete-field="first" placeholder="Имя">
                            </div>
                            <div class="field">
                                <input class="field__input" type="text" name="lastname" value="<?=$arUser["UF_GUESTS_DATA"][$i]["lastname"] ?? ''?>" data-autocomplete-field="middle" placeholder="Отчество">
                            </div>

                            <ul class="list list_autocomplete" data-autocomplete-list></ul>
                        </div>

                        <label class="checkbox">
                            <input type="checkbox" name="save"
                                    <?if($arUser["UF_GUESTS_DATA"][$i]):?>checked<?endif;?>
                                    value
                                    <?if(!$arUser["UF_GUESTS_DATA"][$i]):?>disabled<?endif;?>
                                    data-check-disabled-control
                            >
                            <span>Сохранить данные гостя для будущих бронирований</span>
                        </label>
                    </div>
                <?endfor;*/?>

                <div class="reservation-form__fields-item">
                    <div class="reservation-form__fields-label"><?=Loc::getMessage('ORDER_COMMENT')?></div>
                    <div class="field">
                        <textarea class="field__input" name="comment" placeholder="Введите дополнительную информацию или пожелания к заказу"><?=$arUser['COMMENT']?></textarea>
                    </div>
                </div>
            </div>            
        </div>
    </div>    
    <div class="reservation-form__summary" id="coupon-block">        
        <div class="reservation-form__summary-holder">
            <p class="reservation-form__right-title"><?=Loc::getMessage('FORM_RIGHT_TITLE')?></p>
            <div class="reservation-form__pre-final">
                <div class="reservation-form__nights">
                    <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?>
                </div>
                <div class="reservation-form__pre-total <?=$arResult['finalPrice']['REAL_DISCOUNT'] != 0 ? 'discount' : ''?>">
                    <?= number_format($totalPrice, 0, '.', ' ') ?> <?=Loc::getMessage('ORDER_RUBLE')?>
                </div>
            </div>
            <label class="form__coupons-toggler checkbox">
                <input id="coupon_toggler" type="checkbox" value="" <?=$arResult['coupons'][0] ? 'checked' : ''?>>
                <span><?=Loc::getMessage('COUPON_HAS')?></span>
            </label>            
            <?if (count($arResult['coupons'])):?>                            
                <div id="form__coupons" class="form__coupons coupon__entered">                
                    <input type="text" class="field__input coupon__input" value="<?=$arResult['coupons'][0]['COUPON']?>">
                    <svg class="coupon__success-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 18 17" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.6574 2.53062L5.39302 16.8907L0.24707 7.42242L2.84543 5.8302L5.93478 11.5145L15.4351 0.390717L17.6574 2.53062Z" fill="#34A453"/>
                    </svg>
                    <button type="button" onclick="order.removeCoupon('<?=$arResult['coupons'][0]['COUPON']?>');" class="coupon__delete">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.2449 7.98458L1.46104 13.3204L3.49136 15.1407L8.27522 9.80487L13.8369 14.7912L15.6572 12.7609L10.0955 7.77455L15.3077 1.961L13.2773 0.140717L8.06519 5.95426L2.47751 0.944625L0.657227 2.97494L6.2449 7.98458Z" fill="#E63623"/>
                        </svg>
                    </button>
                    <span class="reservation-form__discount-price"><?= number_format($arResult['finalPrice']['REAL_PRICE'], 0, '.', ' ') ?> <?=Loc::getMessage('ORDER_RUBLE')?></span>
                </div>                
            <?else:?>   
                <div id="form__coupons" class="form__coupons" style="display: none">                
                    <input placeholder="<?=Loc::getMessage('COUPON_INVITE')?>" type="text" class="field__input coupon__input">
                    <button type="button" onclick="order.sendCoupon();" class="coupon__enter button button_primary"><?=Loc::getMessage('COUPON_ENTER')?></button>
                </div>                
            <?endif;?>
            <div class="reservation-form__price">
                <span>Итого</span>
                <div class="h1"><?= number_format($arResult['finalPrice']['REAL_PRICE'], 0, '.', ' ') ?> <?=Loc::getMessage('ORDER_RUBLE')?></div>
            </div>
                         
            <?php if (
                $arResult['finalPrice']['REAL_PRICE'] > floatval(Users::getInnerScore())
                && intval(Users::getInnerScore()) !== 0
                && $isAuthorized
            ):?>
                <div class="reservation-form__price-cert__wrapper">
                    <div class="reservation-form__price-cert__item">
                        <span>Ваш баланс</span>
                        <span>
                            <?=number_format(Users::getInnerScore(), 0, '.', ' ')?> ₽
                        </span>
                    </div>
                    <div class="reservation-form__price-cert__item">
                        <span>Доплата</span>
                        <span>
                            <?=number_format($arResult['finalPrice']['REAL_PRICE'] - Users::getInnerScore(), 0, '.', ' ')?> ₽
                        </span>
                    </div>
                </div>                
            <? elseif (
                $arResult['finalPrice']['REAL_PRICE'] <= floatval(Users::getInnerScore())
                && intval(Users::getInnerScore()) !== 0
                && $isAuthorized
            ): ?>
                <div class="reservation-form__price-cert__wrapper">
                    <div class="reservation-form__price-cert__item">
                        <span>Ваш баланс</span>
                        <span>
                            <?=number_format(Users::getInnerScore(), 0, '.', ' ')?> ₽
                        </span>
                    </div>
                    <div class="reservation-form__price-cert__item">
                        <span>Остаток на счёте</span>
                        <span>
                            <?=number_format(Users::getInnerScore() - $arResult['finalPrice']['REAL_PRICE'], 0, '.', ' ')?> ₽
                        </span>
                    </div>
                </div>      
            <? endif; ?>            
            
            <div class="payment-block" <?=Users::getInnerScore() - $arResult['finalPrice']['REAL_PRICE'] >= 0 ? 'style="display:none"' : ''?>>
                <p class="payment-block__title">Выберите способ оплаты:</p>
                <div class="payment-methods">
                    <?if (is_array($arResult['paySystems'])) {
                        foreach ($arResult['paySystems'] as $key => $paysystem) {?>                            
                            <?if ($paysystem['ID'] != YANDEX_SPLIT_PAYSYSTEM_ID) {?>                                
                            <?$img = CFile::getFileArray($paysystem['LOGOTIP'])?>
                            <label class="checkbox payment-item">
                                <input type="radio" class="checkbox" value="<?=$paysystem['ID']?>" name="paysystem" <?=$key == 0 ? 'checked' : ''?>>
                                <span></span>
                                <img src="<?=$img['SRC']?>" width="<?=intval($img['WIDTH'])/2?>">
                                <p><?=$paysystem['NAME']?></p>
                            </label> 
                            <?} else {?> 
                                <label class="checkbox payment-item">
                                    <input type="radio" class="checkbox" value="<?=$paysystem['ID']?>" name="paysystem" <?=$key == 0 ? 'checked' : ''?>>
                                    <span></span>
                                    <yandex-pay-badge
                                        merchant-id="d82873ad-61ce-4050-b05e-1f4599f0bb7b"
                                        type="bnpl"
                                        amount="<?=$arResult['finalPrice']['REAL_PRICE'] - Users::getInnerScore()?>"
                                        size="l"
                                        variant="detailed"
                                        theme="light"
                                        color="primary"
                                    />
                                </label> 
                            <?}?>
                        <?}
                    }?>
                </div>
            </div>
            
            <?/* if ($isAuthorized): */?>
                <button class="button button_primary" type="button" data-form-submit data-order><?=Loc::getMessage('ORDER_PAY')?></button>
            <? /*else: ?>
                <button class="button button_primary" id="order__confirm-data" type="button" data-form-submit data-order><?=Loc::getMessage('ORDER_CONFIRM_ACTION')?></button>
            <? endif; */?>
            <div class="reservation-form__footnote">
                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" name="personal_data" value="1">
                        <span><?=Loc::getMessage("ORDER_POLITICS", Array ("#LINK#" => "/agreement/"))?></span>
                    </label>
                </div>
                <?if($arSection["UF_EXTERNAL_SERVICE"] == 1):?>
                    <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" name="cancel_policy" value="1">
                            <span><?=Loc::getMessage("ORDER_CANCELLATION_LINK", Array ("#LINK#" => "#", "#DATA_ATTR#" => "data-get-cancellation-amount"))?></span>
                        </label>
                    </div>
                <?else:?>
                    <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" name="cancel_policy" value="1">
                            <span><?=Loc::getMessage("ORDER_CANCELLATION_LINK", Array ("#LINK#" => "#", "#DATA_ATTR#" => "data-get-cancellation-amount-bnovo"))?></span>
                        </label>
                    </div>
                <?endif;?>
                <div><?=Loc::getMessage('ORDER_SSL')?></div>
            </div>           
        </div>
    </div>
</form>

<div class="modal modal_cancel" id="cancel">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3"><?=Loc::getMessage('ORDER_POPUP_TITLE')?></div>

        <div class="modal__content">
            <ul class="list" data-resevation-list-free style="display: none;">
                <li class="list__item reservation-date"><?=Loc::getMessage('ORDER_FREE_CANCELLATION_TIME')?></li>
                <li class="list__item reservation-penalty"><?=Loc::getMessage('ORDER_CANCELLATION_FEE_FROM')?><span></span> <?=Loc::getMessage('ORDER_RUBLE')?></li>
            </ul>
            <ul class="list" data-resevation-list style="display: none;">
                <li class="list__item reservation-penalty"><?=Loc::getMessage('ORDER_CANCELLATION_FEE')?><span></span> <?=Loc::getMessage('ORDER_RUBLE')?></li>
            </ul>

            <div class="modal__content-control">
                <button class="button button_primary order_cancel_button" data-modal-close><?=Loc::getMessage('ORDER_MODAL_CLOSE')?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_cancel" id="cancelBnovo">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3"><?=Loc::getMessage('ORDER_POPUP_TITLE')?></div>

        <div class="modal__content">
            <ul class="list" data-resevation-list-free style="display: none;">
                <li class="list__item reservation-date"><?=Loc::getMessage('ORDER_FREE_CANCELLATION_TIME')?></li>
                <li class="list__item reservation-penalty"><?=Loc::getMessage('ORDER_CANCELLATION_FEE_FROM')?><span></span> <?=Loc::getMessage('ORDER_RUBLE')?></li>
            </ul>
            <ul class="list" data-resevation-list style="display: none;">
                <li class="list__item reservation-penalty"><?=Loc::getMessage('ORDER_CANCELLATION_FEE')?><span></span> <?=Loc::getMessage('ORDER_RUBLE')?></li>
            </ul>

            <div class="modal__content-control">
                <button class="button button_primary order_cancel_button" data-modal-close><?=Loc::getMessage('ORDER_MODAL_CLOSE')?></button>
            </div>
        </div>
    </div>
</div>

<script>
    window.orderData = {
        prodName: '<?=$arResult['arElement']['NAME']?>',
        price: <?=$arResult['finalPrice']['REAL_PRICE']?>,
        prodID: <?=$arResult['arElement']['ID']?>,
        sectionName: '<?=$arResult['arSection']['NAME']?>'
    };

    const autocomplete = [
        <?foreach($arUser["UF_GUESTS_DATA"] as $key => $arItem):?>
        {
            id: '<?=$key?>',
            last: '<?=$arItem["surname"]?>',
            first: '<?=$arItem["name"]?>',
            middle: '<?=$arItem["lastname"]?>'
        },
        <?endforeach;?>
    ];
    var coockiePrefix = "<?=COption::GetOptionString("main", "cookie_name", "BITRIX_SM");?>";    
</script>