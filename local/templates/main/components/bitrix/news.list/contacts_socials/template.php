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
			<a href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>" target="_blank">
				<svg class="icon icon_<?=$arItem["CODE"]?>" viewbox="0 0 <?=$arItem["PROPERTIES"]["WIDTH"]["VALUE"]?> <?=$arItem["PROPERTIES"]["HEIGHT"]["VALUE"]?>" style="width: <?=round($arItem["PROPERTIES"]["WIDTH"]["VALUE"]/10, 1)?>rem; height: <?=round($arItem["PROPERTIES"]["HEIGHT"]["VALUE"]/10, 1)?>rem;">
					<use xlink:href="#<?=$arItem["CODE"]?>" />
				</svg>
				<span><?=$arItem["NAME"]?></span>
			</a>
		</li>
	<?endforeach;?>
<?endif;?>