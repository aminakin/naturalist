<?php 

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
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

echo '<pre>';
// print_r($arResult);
echo '</pre>';

?>

<div class="certificates_index__wrapper">
    <div class="certificates_index__inner">
        <div class="container">
            <div class="certificates_index">
                <div class="certificates_index__header">
                    <div class="certificates_index__header-title">
                        <?php
                            $APPLICATION->IncludeComponent(
                                "bitrix:main.include",
                                "",
                                Array(
                                    "AREA_FILE_SHOW" => "file", 
                                    "PATH" => $templateFolder . '/include_areas/title.php',
                                    "EDIT_TEMPLATE" => ""
                                )
                            );
                        ?>
                    </div>
                    <div class="certificates_index__header-preview">
                        <div class="certificates_index__header-preview-content">
                            <div class="certificates_index__header-preview-title">
                                <?php
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        Array(
                                            "AREA_FILE_SHOW" => "file", 
                                            "PATH" => $templateFolder . '/include_areas/preview-title.php',
                                            "EDIT_TEMPLATE" => ""
                                        )
                                    );
                                ?>
                            </div>
                            <div class="certificates_index__header-preview-subtitle">
                                <?php
                                    $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        Array(
                                            "AREA_FILE_SHOW" => "file", 
                                            "PATH" => $templateFolder . '/include_areas/preview-subtitle.php',
                                            "EDIT_TEMPLATE" => ""
                                        )
                                    );
                                ?>
                            </div>
                        </div>
                        <div class="certificates_index__header-preview-background">
                            <?php
                                $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    Array(
                                        "AREA_FILE_SHOW" => "file", 
                                        "PATH" => $templateFolder . '/include_areas/preview-img.php',
                                        "EDIT_TEMPLATE" => ""
                                    )
                                );
                            ?>
                        </div>
                    </div>
                </div>
                <? if (!empty($arResult['STEPS'])): ?>
                    <div class="certificates_index__steps">
                        <? foreach ($arResult['STEPS'] as $arStepNumeration => $arStep): ?>
                            <div class="certificates_index__step">
                                <div class="certificates_index__step-header">
                                    <div class="certificates_index__step-ico">
                                        <?=$arStepNumeration + 1?>
                                    </div>
                                    <div class="certificates_index__step-title">
                                        <?=$arStep['NAME']?>
                                    </div>
                                </div>
                                <div class="certificates_index__step-text">
                                    <?=$arStep['PREVIEW_TEXT']?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>
                <div class="certificates_index__btns">
                    <div class="certificates_index__btn orange">
                        <a href="buy/">Купить</a>
                    </div>
                    <div class="certificates_index__btn blue">
                        <a href="activate/">Активировать</a>
                    </div>
                    <div class="certificates_index__btn transparent">
                        <a data-modal href="#corporat">Для корпоративных клиентов</a>
                    </div>
                </div>
                <? if (!empty($arResult['QUESTIONS'])): ?>
                    <div class="certificates_index__questions-title">
                        <span>Часто задаваемые вопросы</span>
                    </div>
                    <div class="certificates_index__questions">
                        <? foreach ($arResult['QUESTIONS'] as $arQuestion): ?>
                            <div class="certificates_index__question">
                                <div class="certificates_index__question-title">
                                    <?=$arQuestion['NAME']?>
                                </div>
                                <div class="certificates_index__question-text">
                                    <?=$arQuestion['PREVIEW_TEXT']?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="modal modal_form" id="corporat">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>        
        <div class="modal__footnote"><span>Сертификат на загородный отдых</span> - идеальный подарок для коллег, партнеров и клиентов! Оставьте ваши контакты и мы молниеносно с вами свяжемся ;)</div>

        <form class="form form_validation" id="form-corporat">
            <div class="form__item">
                <span>Как к вам обращаться?</span>
                <div class="field">
                    <input class="field__input" type="text" name="name" placeholder="Имя">
                </div>
            </div>
            <div class="form__item">
                <span>Ваш телефон</span>
                <div class="field">
                    <input class="field__input" type="tel" name="phone" placeholder="Телефон">
                </div>
            </div>
            <div class="form__item">
                <span>E-mail</span>
                <div class="field">
                    <input class="field__input" type="text" name="email" placeholder="E-mail">
                </div>
            </div>            
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="cancel_policy" value="1">
                    <span>Я даю своё согласие на обработку указанных данных на условиях <a href="/agreement/">Политики конфиденциальности</a> в целях создания и обработки заявки и осуществления обратной связи по вопросам её рассмотрения.</span>
                </label>
            </div>            
            <div class="form__controls">
                <button class="button" data-form-corporat-send data-form-submit>Отправить</button>
            </div>
        </form>
    </div>
</div>