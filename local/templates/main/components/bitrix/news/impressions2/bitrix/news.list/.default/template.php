<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
