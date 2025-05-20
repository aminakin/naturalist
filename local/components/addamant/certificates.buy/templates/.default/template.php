<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Localization\Loc;

/** @var  $arResult */
/** @var  $arParams */
/** @var  $templateFolder */

Loc::loadMessages(__FILE__);

?>

<?php 
if ($arResult['PAYMENT_URL']) {
    LocalRedirect($arResult['PAYMENT_URL'], true);    
}

if ($arResult['ERROR']) {
    echo $arResult['ERROR'];
} else {
?>

<section class="container cert-buy">
    <form action="" class="cert-buy__form" method="post">
        <input type="hidden" name="fromOrder" value="Y">
        <input type="hidden" name="is_cert" value="Y">
        <input type="hidden" name="type" value="phone">
        <input type="hidden" name="variant_cost" value="<?=$arParams['VARIANT_COST']?>">
        <input type="hidden" name="pocket_cost" value="<?=$arParams['POCKET_COST']?>">
        <section class="form__block nominal">
            <div class="form__title-block">
                <span class="form__number">1.</span>
                <p class="form__title"><?=Loc::GetMessage('NOMINAL_TITLE')?></p>
                <span class="form__dot"></span>
            </div>
            <?php if (!empty($arResult['CERTIFICATES'])) {?>
                <div class="form__certs">
                    <?php foreach ($arResult['CERTIFICATES'] as $cert) {?>
                        <div class="form__cert">
                            <label 
                                class="cert--<?=$cert['ID']?>"
                                variant="
                                    <?php foreach ($cert['VARIANT'] as $key => $certVariant) {
                                        echo $certVariant['UF_FILE'] . ';';
                                    } ?>
                                " 
                                el-variant="
                                    <?php foreach ($cert['VARIANT_EL'] as $key => $certElVariant) {
                                        echo $certElVariant['UF_FILE'] . ';';
                                    } ?>
                                " 
                                pocket="
                                    <?php foreach ($cert['POCKET'] as $key => $certPocket) {
                                        echo $certPocket['UF_FILE'] . ';';
                                    } ?>
                                ">
                                <input cost="<?=$cert['PRICE']?>" type="radio" name="cert_id" value="<?=$cert['ID']?>" class="visually-hidden" required>
                                <?php if ($cert['PRICE'] != 0) {?>
                                    <span style="color:<?=$cert['COLOR'] ? $cert['COLOR'] : 'black'?>" class="nominal__price"><?=CurrencyFormat($cert['PRICE'], 'RUB')?></span>
                                </label>
                                <?php } else {?>
                                    <input target="<?=$cert['ID']?>" style="color:<?=$cert['COLOR'] ? $cert['COLOR'] : 'black'?>" type="number" min="<?=$arParams['MIN_COST']?>" name="cert_price" placeholder="0000 ₽" class="nominal__custom-cost nominal__price">                                
                                </label>
                                <p class="nominal__descr"><?=Loc::GetMessage('NOMINAL_DESCR', ['#COST#' => $arParams['MIN_COST']])?></p>
                                <?php } ?>                        
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>
        <section class="form__block format">
            <div class="form__title-block">
                <span class="form__number">2.</span>
                <p class="form__title"><?=Loc::GetMessage('FORMAT_TITLE')?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__format">
                <div class="format__electro">
                    <label>
                        <input type="radio" name="cert_format" value="electro" class="visually-hidden" required>
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none">
                            <g clip-path="url(#clip0_4879_59206)">
                                <path d="M32 0C14.3262 0 0 14.3262 0 32C0 49.6738 14.3262 64 32 64C49.6738 64 64 49.6707 64 32C64 14.3293 49.6707 0 32 0Z" fill="#1B2D50"/>
                                <path d="M31.8931 52.4548C27.1307 52.4548 22.9905 51.5869 19.4724 49.8509C15.9543 48.1149 13.2655 45.7257 11.4062 42.6833C9.54676 39.6418 8.61707 36.1854 8.61707 32.3141C8.61707 28.4428 9.55429 24.9859 11.4287 21.9435C13.3022 18.901 16.0115 16.5118 19.5567 14.7758C23.1019 13.0399 27.2732 12.1719 32.0707 12.1719C36.8632 12.1719 41.0345 13.0323 44.5847 14.7532C48.1349 16.4742 50.8442 18.8323 52.7126 21.8276C54.5981 24.8249 55.5409 28.2125 55.5409 31.9905C55.5409 34.2643 55.2107 36.1618 54.5505 37.6831C53.8902 39.2043 52.9369 40.3412 51.6906 41.0938C50.4474 41.8464 48.9648 42.2227 47.2428 42.2227C45.2771 42.2227 43.7027 41.8077 42.5196 40.9779C41.3478 40.168 40.5173 38.953 40.1881 37.5672H39.6357C38.9283 38.9499 37.8295 40.0718 36.3394 40.9327C34.8492 41.7937 32.9211 42.2237 30.555 42.2227C28.4357 42.2227 26.5843 41.8077 25.0009 40.9779C23.472 40.2004 22.2057 38.9901 21.3599 37.4979C20.516 36.0088 20.0935 34.2809 20.0925 32.3141C20.0915 30.3474 20.514 28.6189 21.3599 27.1288C22.2 25.6422 23.4554 24.4329 24.9723 23.6488C26.5387 22.818 28.3825 22.403 30.5038 22.404C32.7766 22.404 34.628 22.8496 36.0579 23.7406C37.4878 24.6317 38.4787 25.7069 39.0306 26.9662H39.7365L38.8154 22.8646H43.0931V34.3416C43.0931 35.5708 43.4237 36.5156 44.085 37.1758C44.7463 37.8361 45.6754 38.1667 46.8726 38.1677C47.7636 38.1677 48.5012 37.9756 49.0852 37.5913C49.6692 37.2069 50.1147 36.5547 50.4218 35.6345C50.7288 34.7134 50.8823 33.4997 50.8823 31.9935C50.8823 28.9511 50.1453 26.2392 48.6712 23.858C47.1972 21.4769 45.0398 19.6104 42.199 18.2588C39.3532 16.9051 35.9796 16.2283 32.0782 16.2283C28.1768 16.2283 24.8042 16.9117 21.9604 18.2784C19.1187 19.6461 16.9612 21.5436 15.4882 23.9709C14.0151 26.3983 13.2776 29.1793 13.2756 32.3141C13.2756 35.4479 14.0131 38.2284 15.4882 40.6558C16.9633 43.0831 19.0986 44.9807 21.8942 46.3484C24.6908 47.7151 28.0248 48.3989 31.8961 48.3999C33.855 48.4224 35.8105 48.2291 37.7271 47.8234C39.3218 47.4761 40.8745 46.9583 42.3586 46.2791L44.2491 49.741C42.8964 50.5699 41.1905 51.2301 39.1315 51.7218C37.0724 52.2135 34.6596 52.4579 31.8931 52.4548ZM31.6176 38.1677C33.0606 38.1677 34.2973 37.9219 35.3279 37.4302C36.2966 36.9919 37.1085 36.2683 37.6549 35.3561C38.1693 34.4247 38.4391 33.3781 38.4391 32.3141C38.4391 31.2501 38.1693 30.2035 37.6549 29.2722C37.1089 28.3597 36.2969 27.6359 35.3279 27.198C34.2963 26.7053 33.0596 26.4595 31.6176 26.4605C30.1426 26.4605 28.8882 26.7063 27.8547 27.198C26.8814 27.6391 26.0626 28.3615 25.5036 29.2722C24.9749 30.1988 24.6968 31.2472 24.6968 32.3141C24.6968 33.381 24.9749 34.4294 25.5036 35.3561C26.063 36.2664 26.8817 36.9887 27.8547 37.4302C28.8882 37.9209 30.1426 38.1667 31.6176 38.1677Z" fill="#F18D3F"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_4879_59206">
                                    <rect width="64" height="64" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <span class="format__title"><?=Loc::GetMessage('ELECTRO_NAME')?></span>
                    </label>
                    <p class="format__descr"><?=Loc::GetMessage('ELECTRO_DESCR')?></p>                    
                </div>
                <div class="format__fiz">
                    <label>
                        <input type="radio" name="cert_format" value="fiz" class="visually-hidden" required checked>
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none">
                            <g clip-path="url(#clip0_4879_59214)">
                                <path d="M32 0C14.3262 0 0 14.3262 0 32C0 49.6738 14.3262 64 32 64C49.6738 64 64 49.6707 64 32C64 14.3293 49.6707 0 32 0Z" fill="#E2C189"/>
                                <path d="M45.6294 21.6875H18.5619C18.3125 21.6875 18.1104 21.8897 18.1104 22.1391V49.2066C18.1104 49.456 18.3125 49.6581 18.5619 49.6581H45.6294C45.8788 49.6581 46.081 49.456 46.081 49.2066V22.1391C46.081 21.8897 45.8788 21.6875 45.6294 21.6875Z" fill="#1B2D50"/>
                                <path d="M46.081 33.2753H33.8063V22.2378C39.0488 21.5786 42.4671 17.7509 42.4671 14.1535C42.4793 13.5225 42.3608 12.8957 42.1191 12.3127C41.8774 11.7296 41.5178 11.2028 41.0627 10.7654C40.411 10.1543 39.2204 9.42578 37.2501 9.42578C35.3205 9.42578 33.6739 10.1362 32.4863 11.4818C32.3538 11.6324 32.2304 11.7919 32.1115 11.9545C31.9941 11.7919 31.8707 11.6339 31.7367 11.4818C30.5551 10.1362 28.8994 9.42578 26.9743 9.42578C25.004 9.42578 23.8135 10.1543 23.1617 10.7654C22.707 11.2021 22.3476 11.7281 22.1059 12.3104C21.8642 12.8927 21.7456 13.5187 21.7574 14.149C21.7574 16.1057 22.811 18.1829 24.5811 19.7182C26.0953 21.0367 28.043 21.8886 30.1939 22.1972V33.2662H18.1104V36.8787H30.1939V49.6501H33.8063V36.8877H46.081V33.2753ZM35.1971 13.8706C35.6983 13.3031 36.35 13.0382 37.2501 13.0382C37.8658 13.0382 38.3414 13.1661 38.5913 13.3994C38.6848 13.4999 38.7564 13.6186 38.8018 13.7481C38.8471 13.8777 38.8651 14.0152 38.8547 14.152C38.8547 14.8294 38.4392 15.9838 37.2697 17.0013C36.315 17.8152 35.1586 18.357 33.9222 18.5697C33.9222 18.5411 33.9222 18.5125 33.9222 18.4839C33.9583 16.9426 34.2368 14.9603 35.1971 13.8706ZM25.3698 14.149C25.3593 14.0122 25.3774 13.8747 25.4227 13.7451C25.468 13.6156 25.5397 13.4969 25.6332 13.3964C25.8816 13.1631 26.3587 13.0352 26.9743 13.0352C27.8774 13.0352 28.5231 13.2986 29.0229 13.8645C29.6957 14.6171 30.1231 15.9326 30.2601 17.6485C30.2842 17.9601 30.2962 18.2506 30.3023 18.5366C30.3015 18.5456 30.3015 18.5547 30.3023 18.5637C27.0165 17.9345 25.3698 15.5654 25.3698 14.149Z" fill="#F18D3F"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_4879_59214">
                                    <rect width="64" height="64" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <span class="format__title"><?=Loc::GetMessage('FIZ_NAME', ['#COST#' => $arParams['VARIANT_COST'] . '/' . $arParams['VARIANT_COST'] + $arParams['POCKET_COST']])?></span>
                    </label>
                    <p class="format__descr">
                        <?=Loc::GetMessage('FIZ_DESCR', ['#COST#' => $arParams['VARIANT_COST'], '#COST_1#' => $arParams['VARIANT_COST'] + $arParams['POCKET_COST']])?>
                        <span><?=Loc::GetMessage('FIZ_FOOTNOTE')?></span>
                    </p>  
                </div>
            </div>
        </section>
        <section class="form__block el-variant design" style="display: none">
            <div class="form__title-block">
                <span class="form__number">3.</span>
                <p class="form__title"><?=Loc::GetMessage('VARIANT_EL_TITLE')?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__el-variant">
                <?php foreach ($arResult['VARIANT_EL'] as $elVariant) {?>
                    <label>
                        <input type="radio" name="cert_el_variant" value="<?=$arResult['LOCAL_HOST'].CFile::getPath($elVariant['UF_FILE'])?>" class="visually-hidden" >
                        <span class="el-variant__title"><?=$elVariant['UF_NAME']?></span>
                        <div class="variant__img-wrap">
                            <img src="<?=CFile::ResizeImageGet($elVariant['UF_FILE'], array('width' => 600, 'height' => 849), BX_RESIZE_IMAGE_EXACT, true)['src']?>" alt="">
                        </div>                        
                    </label>
                <?php } ?>                
            </div>
        </section>
        <section class="form__block variant design">
            <div class="form__title-block">
                <span class="form__number">3.</span>
                <p class="form__title"><?=Loc::GetMessage('VARIANT_TITLE', ['#COST#' => $arParams['VARIANT_COST']])?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__el-variant">
                <?php foreach ($arResult['VARIANT'] as $variantKey => $variant) {?>
                    <label>
                        <input type="radio"
                               cost="<?=$arParams['VARIANT_COST']?>"
                               name="cert_variant"
                               value="<?=$arResult['LOCAL_HOST'].CFile::getPath($variant['UF_IMG_TO_CERT'])?>"
                               class="visually-hidden"
                            <?$variantKey == 0 ? 'required' : ''?>
                               data-variant-key="<?=$variantKey?>"
                        onclick="updateHiddenImageField(this)">
                        <span class="el-variant__title"><?=$variant['UF_NAME']?></span>
                        <div class="variant__img-wrap">
                            <img src="<?=CFile::ResizeImageGet($variant['UF_FILE'], array('width' => 600, 'height' => 763), BX_RESIZE_IMAGE_EXACT, true)['src']?>" alt="">
                        </div>
                    </label>
                <?php } ?>
            </div>
            <input type="hidden" name="cert_variant_back" id="cert_variant_back" value="">
        </section>
        <section class="form__block pocket design" style="display: none">
            <div class="form__title-block">
                <span class="form__number">4.</span>
                <p class="form__title"><?=Loc::GetMessage('POCKET_TITLE', ['#COST#' => $arParams['POCKET_COST']])?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__el-variant">
                <?php foreach ($arResult['POCKET'] as $pocket) {?>
                    <label>
                        <input cost="<?=$arParams['POCKET_COST']?>" type="checkbox" name="cert_pocket" value="<?=$arResult['LOCAL_HOST'].CFile::getPath($pocket['UF_FILE'])?>" class="visually-hidden">
                        <span class="el-variant__title"><?=$pocket['UF_NAME']?></span>
                        <div class="variant__img-wrap">
                            <img src="<?=CFile::ResizeImageGet($pocket['UF_FILE'], array('width' => 600, 'height' => 763), BX_RESIZE_IMAGE_EXACT, true)['src']?>" alt="">
                        </div>                                        
                    </label>
                <?php } ?>                
            </div>
        </section>
        <section class="form__block congrats">
            <div class="form__title-block">
                <span class="form__number">5.</span>
                <p class="form__title"><?=Loc::GetMessage('VARIANT_TEXT_TITLE')?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__congrats">
                <div class="congrats__content">
                    <p class="congrats__text"><?=Loc::GetMessage('CONGRATS_INPUT_TITLE')?></p>
                    <textarea name="congrats" placeholder="<?=Loc::GetMessage('CONGRATS_INPUT_PLACEHOLDER')?>" maxlength="150"></textarea>
                    <p class="congrats__footnote"><?=Loc::GetMessage('CONGRATS_INPUT_FOOTNOTE')?></p>
                </div>
            </div>
        </section>
        <section class="form__block user-data">
            <div class="form__title-block">
                <span class="form__number">6.</span>
                <p class="form__title"><?=Loc::GetMessage('PERSONAL_TITLE')?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__user-data">
                <div class="user-data__content">
                    <div class="user-data__block">
                        <p class="user-data__title"><?=Loc::GetMessage('USER_DATA_TITLE')?></p>
                        <input type="text" required name="name" placeholder="<?=Loc::GetMessage('USER_DATA_NAME')?>">
                        <input type="text" required name="last_name" placeholder="<?=Loc::GetMessage('USER_DATA_LAST_NAME')?>">
                        <input type="tel" required name="login" placeholder="<?=Loc::GetMessage('USER_DATA_PHONE')?>">
                        <input type="email" required name="email" placeholder="<?=Loc::GetMessage('USER_DATA_EMAIL')?>">
                    </div>
                    <div class="user-data__block electro" style="display: none">
                        <p class="user-data__title electro"><?=Loc::GetMessage('USER_DATA_ELECTRO_TITLE')?></p>
                        <input type="text" name="gift_name" placeholder="<?=Loc::GetMessage('USER_DATA_GIFT_NAME')?>">                                                
                        <input type="email" name="gift_email" placeholder="<?=Loc::GetMessage('USER_DATA_GIFT_EMAIL')?>">
                    </div>
                    <div class="user-data__block fiz" style="display: none">
                        <div class="user-data__block city">
                            <p class="user-data__block-title"><?=Loc::GetMessage('USER_DATA_CITY_TITLE')?></p>
                            <div class="user-data__inputs">
                                <label class="checkbox">
                                    <input type="radio" location="inner" name="city" value="Москва в пределах МКАД" class="visually-hidden">
                                    <span>Москва в пределах МКАД</span>
                                </label>
                                <label class="checkbox">
                                    <input type="radio" location="outer" name="city" value="Москва за пределами МКАД" class="visually-hidden">
                                    <span>Москва за пределами МКАД</span>
                                </label>
                            </div>
                        </div>
                        <div class="user-data__block delivery" style="display: none">
                            <p class="user-data__block-title"><?=Loc::GetMessage('USER_DATA_DELIVERY_TITLE')?></p>
                            <div class="user-data__inputs">
                                <?php foreach ($arResult['DELIVERIES'] as $delKey => $delivery) {?>
                                    <label class="checkbox" style="display: none">
                                        <input cost="<?=$delivery['CONFIG']['MAIN']['PRICE']?>" type="radio" name="delivery" value="<?=$delivery['ID']?>" class="visually-hidden">
                                        <span><?=$delivery['DESCRIPTION']?></span>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="user-data__block address">
                            <p class="user-data__title"><?=Loc::GetMessage('USER_DATA_ADDRESS_TITLE')?></p>
                            <input type="text" name="address" placeholder="<?=Loc::GetMessage('USER_DATA_ADDRESS')?>">                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="form__block comment">
            <div class="form__title-block">
                <span class="form__number">7.</span>
                <p class="form__title"><?=Loc::GetMessage('COMMENT_TITLE')?></p>
                <span class="form__dot"></span>
            </div>
            <div class="form__comment">
                <div class="congrats__content">                    
                    <textarea name="comment" placeholder="<?=Loc::GetMessage('COMMENT_INPUT_PLACEHOLDER')?>"></textarea>                    
                </div>
            </div>
        </section>
        <section class="form__block summ">
            <div class="form__title-block">                
                <p class="form__title"><?=Loc::GetMessage('SUMM_TITLE')?></p>                
            </div>
            <div class="summ__content">
                <div class="summ__item prod">
                    <span class="summ__text"><?=Loc::GetMessage('SUMM_PROD')?></span>
                    <span class="summ__price">0 ₽</span>
                </div>
                <div class="summ__item del-var" >
                    <span class="summ__text"><?=Loc::GetMessage('SUMM_DELIVERY')?></span>
                    <span class="summ__price">0 ₽</span>
                </div>
                <div class="summ__item discount" style="display: none">
                    <span class="summ__text"><?=Loc::GetMessage('DISCOUNT')?></span>
                    <span class="summ__price">0 ₽</span>
                </div>
                <div class="summ__item all">
                    <span class="summ__text"><?=Loc::GetMessage('SUMM_ALL')?></span>
                    <div class="summ__prices">
                        <span class="summ__price--old">0 ₽</span>
                        <span class="summ__price">0 ₽</span>
                    </div>
                </div>
                <div class="promo__item-wrap <?=$arResult['COUPONS'] ? 'entered' : ''?>">
                    <label class="checkbox promo__item">
                        <input type="checkbox" class="checkbox visually-hidden" name="promo" <?=$arResult['COUPONS'] ? 'checked' : ''?>>
                        <span><?=Loc::GetMessage('HAVE_PROMO')?></span>
                    </label>
                    <div class="promo__item-input" <?=$arResult['COUPONS'] ? 'style="display: flex"' : ''?>>
                        <input type="text" name="promo_code" placeholder="<?=Loc::GetMessage('ENTER_PROMO')?>" <?=$arResult['COUPONS'] ? 'value="'.$arResult['COUPONS'][0]['COUPON'].'"' : ''?>>
                        <svg class="coupon__success-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.6574 2.53062L5.39302 16.8907L0.24707 7.42242L2.84543 5.8302L5.93478 11.5145L15.4351 0.390717L17.6574 2.53062Z" fill="#34A453"></path>
                        </svg>
                        <button type="button" class="coupon__delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.2449 7.98458L1.46104 13.3204L3.49136 15.1407L8.27522 9.80487L13.8369 14.7912L15.6572 12.7609L10.0955 7.77455L15.3077 1.961L13.2773 0.140717L8.06519 5.95426L2.47751 0.944625L0.657227 2.97494L6.2449 7.98458Z" fill="#E63623"></path>
                            </svg>
                        </button>
                        <button class="promo__button"><?=Loc::GetMessage('BUT_PROMO')?></button>
                    </div>
                    <div class="promo__info"></div>
                </div>
            </div>
        </section>
        <section class="form__block payment">
            <div class="form__title-block">                
                <p class="form__title"><?=Loc::GetMessage('PAYMENT_TITLE')?></p>                
            </div>
            <div class="payment__content">
                <div class="payment__list">
                    <?php foreach ($arResult['PAY_SYSTEMS'] as $key => $paysystem) {?>                           
                        <?if ($paysystem['ID'] != CERT_YANDEX_SPLIT_PAYSYSTEM_ID) {?>                                
                            <?$img = CFile::getFileArray($paysystem['LOGOTIP'])?>
                            <label class="checkbox payment-item" <?=$paysystem['IS_CASH'] == 'Y' ? 'style="display:none" cash="Y"' : ''?>>
                                <input type="radio" class="checkbox visually-hidden" value="<?=$paysystem['ID']?>" name="paysystem" <?=$key == 0 ? 'checked' : ''?>>
                                <span></span>
                                <?if ($img) {?>
                                    <img src="<?=$img['SRC']?>" width="<?=intval($img['WIDTH'])/2?>">
                                <? } ?>
                                <p><?=$paysystem['PSA_NAME']?></p>
                            </label> 
                        <?} else {?> 
                            <label class="checkbox payment-item">
                                <input type="radio" class="checkbox visually-hidden" value="<?=$paysystem['ID']?>" name="paysystem" <?=$key == 0 ? 'checked' : ''?>>
                                <span></span>
                                <div class="split-badge">
                                    <yandex-pay-badge
                                        merchant-id="d82873ad-61ce-4050-b05e-1f4599f0bb7b"
                                        type="bnpl"
                                        amount="5000"
                                        size="l"
                                        variant="detailed"
                                        theme="light"
                                        color="primary"
                                    />
                                </div>
                            </label> 
                        <?}?>
                    <?php }?>
                </div>
                <button type="submit" id="cert-submit" class="form__submit">Купить</button>
                <div class="form__footnote">
                    <div class="field">
                        <label class="checkbox">
                            <input required type="checkbox" class="visually-hidden" name="personal_data" value="">
                            <span><?=Loc::GetMessage('PAYMENT_POLICY', ['#LINK#' => '/agreement/'])?></span>
                        </label>
                    </div>                    
                    <div class="payment__note"><?=Loc::GetMessage('PAYMENT_NOTE')?></div>
                </div>
            </div>
        </section>
    </form>

    <section class="cert-index__reviews">                    
        <?$APPLICATION->IncludeComponent("bitrix:news.list", "cert-reviews", Array(
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "A",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "EDIT_URL" => "result_edit.php",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "LIST_URL" => "result_list.php",
            "SEF_MODE" => "N",
            "SUCCESS_URL" => "",
            "USE_EXTENDED_ERRORS" => "N",
            "WEB_FORM_ID" => "3",
            "COMPONENT_TEMPLATE" => ".default",
            "IBLOCK_TYPE" => "reviews",
            "IBLOCK_ID" => "26",
            "NEWS_COUNT" => "100",
            "SORT_BY1" => "ID",
            "SORT_ORDER1" => "DESC",
            "SORT_BY2" => "SORT",
            "SORT_ORDER2" => "ASC",
            "FILTER_NAME" => "",
            "FIELD_CODE" => array(
                0 => "",
                1 => "",
            ),
            "PROPERTY_CODE" => array(
                0 => "USER_ID",
                1 => "",
            ),
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "PREVIEW_TRUNCATE_LEN" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "SET_TITLE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_LAST_MODIFIED" => "N",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "Y",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "INCLUDE_SUBSECTIONS" => "Y",
            "STRICT_SECTION_CHECK" => "N",
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "PAGER_TEMPLATE" => ".default",
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "Новости",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "SET_STATUS_404" => "N",
            "SHOW_404" => "N",
            "MESSAGE_404" => "",
            ),
            false
        );?>                    
    </section>
    <section class="cert-index__seo-text">
        <?php
            $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                Array(
                    "AREA_FILE_SHOW" => "file", 
                    "PATH" => $templateFolder . '/include_areas/seo-text.php',
                    "EDIT_TEMPLATE" => ""
                )
            );
        ?>        
    </section>
    <a href="#" class="show-more-seo">Раскрыть</a>

</section>



<style>
    <?php foreach ($arResult['CERTIFICATES'] as $style) {?>
        label.selected.cert--<?=$style['ID']?> {
            background-color: <?=$style['BACK_HOVER_COLOR']?>;
            box-shadow: none;
            border-color: transparent;            
        }
        label.selected.cert--<?=$style['ID']?> .nominal__price {
            color: <?=$style['PRICE_HOVER_COLOR']?> !important
        }
    <?php }?>
</style>

<script>
    let buyCert = new BuyCert(<?=$arResult['DISCOUNT_VALUE'] ? $arResult['DISCOUNT_VALUE'] : 0?>, '<?=$arResult['DISCOUNT_TYPE'] ? $arResult['DISCOUNT_TYPE'] : ''?>');
    buyCert.showFiz();
</script>

<?php } ?>