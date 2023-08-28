<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

global $arSettings;
?>

<? if (count($arResult["ITEMS"]) > 0): ?>
    <div class="news-preview__mobile" data-news-container>
        <div class="h1"><?= $arSettings['main_news_title'] ?></div>
        <div class="news-preview__list">
            <? $k = 1; ?>
            <? foreach ($arResult["ITEMS"] as $arItem): ?>
                <div class="news-preview__item<? if ($k > 4): ?> news-preview__item-hidden<? endif; ?>">
                    <div class="news-preview__image">
                        <div><img class="lazy" data-src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["NAME"] ?>" title="Фото - <?= $arItem["NAME"] ?>"></div>
                    </div>
                    <div class="news-preview__title"><?= $arItem["NAME"] ?></div>
                    <a class="news-preview__more"
                       href="<?= $arItem["PROPERTIES"]["EXTERNAL_LINK"]["VALUE"]
                           ? $arItem["PROPERTIES"]["EXTERNAL_LINK"]["VALUE"]
                           : $arItem["PROPERTIES"]["LINK"]["VALUE"] ?>"
                        <?if($arItem["PROPERTIES"]["EXTERNAL_LINK"]["VALUE"]):?>
                            target="_blank"
                        <?endif;?>
                    >
                        <?= !empty($arItem["PROPERTIES"]["TEXT_LINK"]["VALUE"]) ? $arItem["PROPERTIES"]["TEXT_LINK"]["VALUE"] : $arSettings['main_news_button'] ?>
                    </a>
                </div>
                <? $k++; ?>
            <? endforeach; ?>
        </div>
        <? if ($k > 5): ?>
            <div class="news-preview__show">
                <a href="#" data-main-news-showmore>Показать ещё</a>
            </div>
        <? endif; ?>
    </div>

    <div class="slider news-preview__slider" data-slider-heading>
        <div class="slider__heading">
            <div class="h1"><?= $arSettings['main_news_title'] ?></div>
            <div class="slider__heading-controls">
                <div class="swiper-button-prev">
                    <svg class="icon icon_arrow" viewbox="0 0 32 10" style="width: 3.2rem; height: 1rem;">
                        <use xlink:href="#arrow"/>
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg class="icon icon_arrow" viewbox="0 0 32 10" style="width: 3.2rem; height: 1rem;">
                        <use xlink:href="#arrow"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="swiper">
            <div class="swiper-wrapper">
                <? foreach ($arResult["ITEMS"] as $arItem): ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                        <div class="news-preview__item">
                            <div class="news-preview__image">
                                <div><img class="lazy" data-src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arItem["NAME"] ?>" title="Фото - <?= $arItem["NAME"] ?>">
                                </div>
                            </div>
                            <div class="news-preview__title"><?= $arItem["NAME"] ?></div>
                            <a class="news-preview__more"
                               href="<?= $arItem["PROPERTIES"]["EXTERNAL_LINK"]["VALUE"]
                                   ? $arItem["PROPERTIES"]["EXTERNAL_LINK"]["VALUE"]
                                   : $arItem["PROPERTIES"]["LINK"]["VALUE"] ?>"
                                <?if($arItem["PROPERTIES"]["EXTERNAL_LINK"]["VALUE"]):?>
                                    target="_blank"
                                <?endif;?>
                            >
                                <?= !empty($arItem["PROPERTIES"]["TEXT_LINK"]["VALUE"]) ? $arItem["PROPERTIES"]["TEXT_LINK"]["VALUE"] : $arSettings['main_news_button'] ?>
                            </a>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>
<? endif; ?>