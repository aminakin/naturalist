<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

global $arSettings;
?>

<section class="location__wrapper">    
    <div class="container">
        <div class="location__title-wrap">
            <span><?= Loc::getMessage('LOCATION_TITLE')?></span>
            <div class="location__btn-list">
                <a href="#" data-btn="regions" class="location__btn-item active">
                    <?= Loc::getMessage('LOCATION_REGIONS')?>
                </a>
                <a href="#" data-btn="reservoirs" class="location__btn-item">
                    <?= Loc::getMessage('LOCATION_RESERVOIR')?>
                </a>
            </div>
            <a href="" class="link-all"><?= Loc::getMessage('LINK_ALL')?></a>
        </div>
        <div class="location__items-wrap">
            <div class="location__group active" data-group="regions">
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Московская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Ленинградская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Крым</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Алтай</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Краснодарский край</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Дагестан</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Карелия</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Ярославская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Тульская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Калужская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Тверская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Адыгея</span>
                </div>
            </div>
            <div class="location__group" data-group="reservoirs">
            <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Адыгея</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Ярославская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Алтай</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Тверская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Карелия</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Крым</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Калужская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Республика Дагестан</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Ленинградская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Тульская область</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Краснодарский край</span>
                </div>
                <div class="location__item">
                    <img src="<?= $templateFolder?>/img/Frame.png" alt="">
                    <span class="location__item-name">Московская область</span>
                </div>
            </div>
        </div>
        <a class="btn-all"><?= Loc::getMessage('BTN_ALL')?></a>
    </div>
</section>