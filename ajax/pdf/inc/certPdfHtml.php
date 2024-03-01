<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__); ?>

<div class="order-pdf__wrap">
    <div class="cert__image">
        <img width="500" src="<?=$arResult['PROPS']['CERT_FORMAT'] == 'electro' ? $arResult['PROPS']['ELECTRO_VARIANT'] : $arResult['PROPS']['FIZ_VARIANT']?>" alt="">
    </div>
    <div class="order-pdf__logo">
        <img width="250" src="<?=HTTP_HOST?>/ajax/pdf/inc/img/logo.png" alt="">
    </div>    
    <div class="order-pdf__block1">
        <?if ($arResult['PROPS']['PROP_CONGRATS'] == '') {?>
            <div class="order-pdf__congrats-title"><?=Loc::getMessage('PDF_CONGRATS_TITLE');?></div>
            <div class="order-pdf__congrats">                
                <?=Loc::getMessage('PDF_CONGRATS');?> 
            </div>
        <?} else {?>        
            <div class="order-pdf__congrats">
                <?=$arResult['PROPS']['GIFT_NAME'] ? $arResult['PROPS']['GIFT_NAME'].'!<br>' : ''?>
                <?=$arResult['PROPS']['PROP_CONGRATS']?> 
            </div>
        <?}?>
    </div>        
    <div class="order-pdf__line"></div>
    <div class="info">
        <div class="info__1">
            <div class="info__cert">
                <p><?=Loc::getMessage('PDF_DATE_TO');?> <span><?=Loc::getMessage('PDF_TO');?> <?=$arResult['CERT']['UF_DATE_UNTIL']->format("d.m.Y")?></span></p>
                <p><?=Loc::getMessage('PDF_NUMBER');?> <span><?=strtoupper(str_replace('-', '', $arResult['CERT']['UF_CODE']))?></span></p>
            </div>
            <div class="info__contacts">
                <p><?=Loc::getMessage('PDF_PHONE');?></p>
                <p><?=Loc::getMessage('PDF_EMAIL');?></p>
                <p><?=Loc::getMessage('PDF_SITE');?></p>
            </div>
        </div>
        <div class="info__2">
            <img src="<?=HTTP_HOST?>/ajax/pdf/inc/img/qr.png" alt="">
            <p><?=Loc::getMessage('PDF_HOW_TO');?></p>
        </div>
        <div class="clear"></div>
    </div>
    <div class="order-pdf__line"></div>
</div>