<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="object__features">
    <? if ($arResult['ELEMENT']['TEXT'] || $arResult['SELECTED_FEATURES']) { ?>
        <? if ($arResult['SELECTED_FEATURES']) { ?>
            <div class="features__list">
                <? foreach ($arResult['FEATURE_CATEGORIES'] as $feature) { ?>
                    <? if ($feature['LIST']) { ?>
                        <div class="feature__item">
                            <div class="feature__item-title">
                                <img src="<?= $templateFolder . '/img/' . $feature['XML_ID'] . '.svg' ?>" alt="">
                                <?= $feature['VALUE'] ?>
                            </div>
                            <ul class="feature__inner-list">
                                <? foreach ($feature['LIST'] as $value) { ?>
                                    <li><?= $value['UF_NAME'] ?></li>
                                <? } ?>
                            </ul>
                        </div>
                    <? } ?>
                <? } ?>
            </div>
        <? } ?>
        <? if ($arResult['ELEMENT']['TEXT']) { ?>
            <div class="features__text">
                <?= $arResult['ELEMENT']['TEXT'] ?>
            </div>
        <? } ?>
    <? } else { ?>
        Нет дополнительной информации по номеру.
    <? } ?>
</div>