<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>

<? if (!empty($arResult['QUESTIONS'])): ?>
    <h2 class="certificates_index__questions-title">
        Часто задаваемые вопросы
    </h2>
    <div class="certificates_index__questions">
        <? foreach ($arResult['QUESTIONS'] as $arQuestion): ?>
            <div class="certificates_index__question">
                <h3 class="certificates_index__question-title">
                    <?= $arQuestion['NAME'] ?>
                </h3>
                <div class="certificates_index__question-text">
                    <?= $arQuestion['PREVIEW_TEXT'] ?>
                </div>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>