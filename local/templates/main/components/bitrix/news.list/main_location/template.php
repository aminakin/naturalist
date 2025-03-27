<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $arSettings;
?>

<section class="location__wrapper">
    <div class="container">
        <div class="location__title-wrap">
            <span><?= Loc::getMessage('LOCATION_TITLE') ?></span>
            <div class="location__btn-list">
                <a href="#" data-btn="regions" class="location__btn-item active">
                    <?= Loc::getMessage('LOCATION_REGIONS') ?>
                </a>
                <a href="#" data-btn="reservoirs" class="location__btn-item">
                    <?= Loc::getMessage('LOCATION_RESERVOIR') ?>
                </a>
            </div>
            <a href="/regions/" class="link-all"><?= Loc::getMessage('LINK_ALL') ?></a>
        </div>
        <div class="location__items-wrap">
            <div class="location__group active" data-group="regions">
                <? foreach ($arResult['REGIONS'] as $region) { ?>
                    <a href="<?= $region['URL'] ?>" class="location__item">
                        <img width="64" height="64" src="<?= CFile::getPath($region['UF_ICON']) ?>" alt="<?= $region['UF_NAME'] ?>" title="<?= $region['UF_NAME'] ?> РФ">
                        <span class="location__item-name"><?= $region['UF_NAME'] ?></span>
                    </a>
                <? } ?>
            </div>
            <div class="location__group" data-group="reservoirs">
                <? foreach ($arResult['WATER'] as $water) { ?>
                    <a href="<?= $water['URL'] ?>" class="location__item">
                        <img width="64" height="64" src="<?= CFile::getPath($water['UF_IMG']) ?>" alt="">
                        <span class="location__item-name"><?= $water['UF_NAME'] ?></span>
                    </a>
                <? } ?>
            </div>
        </div>
        <a href="/regions/" class="btn-all"><?= Loc::getMessage('BTN_ALL') ?></a>
    </div>
</section>