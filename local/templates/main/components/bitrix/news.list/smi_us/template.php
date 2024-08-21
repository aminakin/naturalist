<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<? if (count($arResult["ITEMS"]) > 0): ?>
    <section class="smi-us">
        <div class="container">
            <div class="smi-us__title"><?= Loc::getMessage('SMI_TITLE')?></div>
            <div class="smi-us__wrapper">
                <? foreach ($arResult["ITEMS"] as $arItem): ?>
                    <?
	            	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	            	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	            	?>
                    <div class="list__item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                        <img src="<?=$arItem['PREVIEW_PICTURE']["SRC"]?>" alt="" title="<?= $arItem["NAME"] ?>">
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </section>
<? endif; ?>