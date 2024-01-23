<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__); ?>

<div class="order-pdf__wrap">
    <div class="order-pdf__logo">
        <img width="496" src="<?=HTTP_HOST?>/ajax/pdf/inc/img/logo.png" alt="">
    </div>
    <div class="order-pdf__line"></div>
    <div class="order-pdf__block1">
        <div class="order-pdf__number">
            <?=Loc::getMessage('PDF_ORDER')?><?=$arResult['ID']?>
        </div>
        <div class="order-pdf__payed">
            <?=Loc::getMessage('PDF_PAYED');?>
        </div>
        <div class="clear"></div>
    </div>        
    <div class="order-pdf__line"></div>
    <div class="order-pdf__room room">
        <div class="room__text">
            <div class="room__name">
                <?=$arResult['ITEMS'][0]['ITEM']['SECTION']['NAME']?>
            </div>
            <div class="room__address">
                <img src="<?=HTTP_HOST?>/ajax/pdf/inc/img/pin.png" alt="" width="12" height="12">
                <span><?=$arResult['ITEMS'][0]['ITEM']['SECTION']['UF_ADDRESS']?></span>
            </div>
            <div class="room__time">
                Заезд&nbsp;&nbsp;<span>с <?=$arResult['ITEMS'][0]['ITEM']['SECTION']['UF_TIME_FROM']?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Выезд&nbsp;&nbsp;<span>до <?=$arResult['ITEMS'][0]['ITEM']['SECTION']['UF_TIME_TO']?></span>
            </div>
        </div>
        <div class="room__photo" style="background-image: url('<?=isset($arResult['IMAGE']) ? HTTP_HOST.$arResult['IMAGE'] : $arResult['IMAGE_URL']?>')"></div>
        <div class="clear"></div>
    </div>
    <div class="order-pdf__line"></div>
    <div class="order-pdf__specs specs">
        <div class="specs__item">
            <img width="20" height="20" src="<?=HTTP_HOST?>/ajax/pdf/inc/img/dates.png" alt="">
            <span><?=$arResult['INTERVAL']?></span>
        </div>
        <div class="specs__item">
            <img width="20" height="20" src="<?=HTTP_HOST?>/ajax/pdf/inc/img/people.png" alt="">
            <span><?=$arResult['PEOPLE']?></span>
        </div>
        <div class="specs__item">
            <img width="20" height="20" src="<?=HTTP_HOST?>/ajax/pdf/inc/img/room.png" alt="">
            <span><?=$arResult['ITEMS'][0]['ITEM']['NAME']?></span>
        </div>
    </div>
    <div class="order-pdf__line"></div>
    <div class="order-pdf__contacts">
        <div class="order-pdf__email"><?=Loc::getMessage('PDF_EMAIL');?></div>
        <div class="order-pdf__phone"><?=Loc::getMessage('PDF_PHONE');?></div>
        <div class="clear"></div>
    </div>
    <div class="order-pdf__guests guests">
        <div class="guests__title">
            <?=Loc::getMessage('PDF_GUEST');?>
        </div>
        <div class="guests__name">
            <?=$arResult['PROPS']['GUEST_LIST'][0]?>
        </div>
    </div>
    <div class="order-pdf__nav">
        <a href="https://yandex.ru/maps?rtext=~<?=$arResult['ITEMS'][0]['ITEM']['SECTION']['UF_COORDS']?>&rtt=auto"><?=Loc::getMessage('PDF_NAV');?></a>
    </div>    
    <div class="order-pdf__bottom bottom">
        <div class="order-pdf__line"></div>        
            <div class="bottom__text">
                <?=Loc::getMessage('PDF_NATURE');?>
            </div>
            <img width="292" height="56" src="<?=HTTP_HOST?>/ajax/pdf/inc/img/nature.png" alt="">        
        <div class="order-pdf__line"></div>
    </div>    
</div>