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
?>
<div class="slider cert-index__slider" data-slider-related>
	<div class="slider__heading">
		<div class="h3">Отзывы Натуралистов</div>

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
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>				
				<div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<div class="cert-review">
						<div class="cert-review__img">
							<?if (is_array($arItem['PROPERTIES']['PHOTOS']['VALUE']) && count($arItem['PROPERTIES']['PHOTOS']['VALUE'])) {
								$arPhoto = CFile::ResizeImageGet($arItem['PROPERTIES']['PHOTOS']['VALUE'][0], array('width' => 356, 'height' => 193), BX_RESIZE_IMAGE_EXACT, true);
							?>
								<img src="<?=$arPhoto['src']?>" alt="">
							<?} else {?>
								<img src="<?=SITE_TEMPLATE_PATH.'/img/camp.jpg'?>" alt="">
							<?}?>
						</div>
						<div class="cert-review__user-name">
							<?
								$User = Bitrix\Main\UserTable::getById($arItem['PROPERTIES']['USER_ID']['VALUE'])->fetchObject();
								echo $User->getName() . ' ' . $User->getLastName();
							?>
						</div>
						<div class="cert-review__text">
							<?=$arItem['PREVIEW_TEXT']?>
						</div>						
					</div>
				</div>
			<? endforeach; ?>
		</div>
		<a href="#" class="cert-index__more">Показать ещё</a>
	</div>
</div>