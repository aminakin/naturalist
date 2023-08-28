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
			<a class="list__link" href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>">
				<div class="list__item-image">
					<div><img class="lazy" data-src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" title="Фото - <?=$arItem["NAME"]?>"></div>
				</div>
				<div class="list__item-title"><?=$arItem["NAME"]?></div>
			</a>
		</li>
	<?endforeach;?>
<?endif;?>