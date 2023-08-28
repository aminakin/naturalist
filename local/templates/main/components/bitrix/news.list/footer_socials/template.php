<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<? if (count($arResult["ITEMS"]) > 0): ?>
    <? foreach ($arResult["ITEMS"] as $arItem): ?>
        <?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>

        <? $onclick = ""; ?>
        <? if ($arParams["FORM"] == "Y" && $arItem["CODE"] == "telegram"): ?>
            <? $onclick = "onclick=\"ym(91071014, 'reachGoal', 'telega'); return true;\""; ?>
        <? elseif ($arParams["FORM"] == "Y" && $arItem["CODE"] == "whatsapp"): ?>
            <? $onclick = "onclick=\"ym(91071014, 'reachGoal', 'whatsapp'); return true;\""; ?>
        <? endif; ?>

        <li class="list__item list__item_<?= $arItem["CODE"] ?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
            <a class="list__link" href="<?= $arItem["PROPERTIES"]["LINK"]["VALUE"] ?>" <?= $onclick; ?> target="_blank">
                <svg class="icon icon_<?= $arItem["CODE"] ?>"
                     viewbox="0 0 <?= $arItem["PROPERTIES"]["WIDTH"]["VALUE"] ?> <?= $arItem["PROPERTIES"]["HEIGHT"]["VALUE"] ?>"
                     style="width: <?= round($arItem["PROPERTIES"]["WIDTH"]["VALUE"] / 10, 1) ?>rem; height: <?= round($arItem["PROPERTIES"]["HEIGHT"]["VALUE"] / 10, 1) ?>rem;">
                    <use xlink:href="#<?= $arItem["CODE"] ?>"/>
                </svg>
                <? if (!$arParams["NO_TEXT"]): ?><span><?= $arItem["NAME"] ?></span><? endif; ?>
            </a>
        </li>
    <? endforeach; ?>
<? endif; ?>