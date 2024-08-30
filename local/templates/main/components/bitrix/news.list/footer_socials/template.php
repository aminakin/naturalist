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

        <li class="list__item list__item_<?= $arItem["CODE"] ?>" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
            <a class="list__link" href="<?= $arItem["PROPERTIES"]["LINK"]["VALUE"] ?>" <?= $onclick; ?> target="_blank">
                <img src="<?= CFile::getPAth($arItem['PROPERTIES']["ICON"]['VALUE']) ?>" alt="" title="<?= $arItem["NAME"] ?>">
            </a>
        </li>
    <? endforeach; ?>
<? endif; ?>