<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
global $arSettings;

?>

<section class="location__wrapper">
    <div class="container">
        <div class="location__title-wrap">
            <h1 class="page_title"><?= Loc::getMessage('LOCATION_TITLE') ?></h1>
            <!-- <span><?//= Loc::getMessage('LOCATION_TITLE') ?></span> -->
            <div class="location__btn-list">
                <a href="#" data-btn="regions" class="location__btn-item active">
                    <?= Loc::getMessage('LOCATION_REGIONS') ?>
                </a>
                <a href="#" data-btn="reservoirs" class="location__btn-item">
                    <?= Loc::getMessage('LOCATION_RESERVOIR') ?>
                </a>
            </div>
        </div>
        <div class="location__items-wrap">
            <div class="location__group active" data-group="regions">
                <div class="location__group-wrap">
                    <? foreach ($arResult['REGIONS'] as $region) { ?>
                        <a href="<?= $region['URL'] ?>" class="location__item">
                            <img width="64" height="64" src="<?= CFile::getPath($region['UF_ICON']) ?>" alt="<?= $region['UF_NAME'] ?>" title="<?= $region['UF_NAME'] ?> РФ">
                            <span class="location__item-name"><?= $region['UF_NAME'] ?></span>
                        </a>
                    <? } ?>
                </div>
                <div class="location-alphabet regions">
                    <?= $arResult['HTML']($arResult['REGIONS_FULL'], 'UF_NAME') ?>
                </div>
                <div class="location-section regions">
                    <? foreach ($arResult['REGIONS_FULL'] as $region) { ?>
                        <a href="<?= $region['URL'] ?>" class="location-full__item hidden"><?= $region['UF_NAME'] ?></a>
                    <? } ?>
                </div>

                <div class="container">
                    <a href="#" class="show-more-seo" id="toggleButton"> <?= Loc::getMessage('MORE_REG') ?></a>
                </div>
            </div>
            <div class="location__group" data-group="reservoirs">
                <div class="location__group-wrap">
                    <? foreach ($arResult['WATER'] as $water) { ?>
                        <a href="<?= $water['URL'] ?>" class="location__item">
                            <img width="64" height="64" src="<?= CFile::getPath($water['UF_IMG']) ?>" alt="">
                            <span class="location__item-name"><?= $water['UF_NAME'] ?></span>
                        </a>
                    <? } ?>
                </div>
                <div class="location-alphabet reservoirs">
                    <?= $arResult['HTML']($arResult['WATER_FULL'], 'UF_NAME') ?>
                </div>
                <div class="location-section reservoirs">
                    <? foreach ($arResult['WATER_FULL'] as $water) { ?>
                        <a href="<?= $water['URL'] ?>" class="location-full__item hidden"><?= $water['UF_NAME'] ?></a>
                    <? } ?>
                </div>
                <? if (isset($arResult['COMMON_WATER']) && !empty($arResult['COMMON_WATER'])) { ?>
                    <div class="location__common-water">
                        <ul class="location__common-water-list">
                            <? foreach ($arResult['COMMON_WATER'] as $key => $commonWater) { ?>
                                <li class="location__common-water-item"><a href="<?= $commonWater['URL'] ?>"><?= $commonWater['UF_NAME'] ?></a></li>
                            <? } ?>
                        </ul>
                    </div>
                <? } ?>
            </div>
        </div>

    </div>
</section>