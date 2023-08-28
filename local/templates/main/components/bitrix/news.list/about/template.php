<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<?if(count($arResult["ITEMS"]) > 0):?>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<li class="list__item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<div class="list__item-image">
				<img class="lazy" data-src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" title="Фото - <?=$arItem["NAME"]?>">
			</div>
			<div class="list__item-content">
				<div class="h3"><?=$arItem["NAME"]?></div>
				<span><?=$arItem["PREVIEW_TEXT"]?></span>
			</div>
		</li>
	<?endforeach;?>
<?endif;?>