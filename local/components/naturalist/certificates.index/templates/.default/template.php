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
                        "SET_TITLE" => "Y",
                        "SET_BROWSER_TITLE" => "Y",
                        "SET_META_KEYWORDS" => "Y",
                        "SET_META_DESCRIPTION" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
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
            </div>
        </div>
    </div>
</div>

<?
    $APPLICATION->IncludeFile("/include/forms/corporat.php", [], [
        "MODE"      => "php",
        "NAME"      => "Редактирование включаемой области раздела",
    ]);
?>