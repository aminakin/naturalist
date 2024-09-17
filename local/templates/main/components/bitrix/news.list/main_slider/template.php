<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('iblock');

global $isMobile;
?>

<? if (count($arResult["ITEMS"]) > 0): ?>
	<div class="main-slider container">
		<div class="<?= count($arResult["ITEMS"]) > 1 ? 'swiper-container' : '' ?>" data-speed="<?= CIBlock::GetArrayByID($arResult["ITEMS"][0]["IBLOCK_ID"], "DESCRIPTION"); ?>">
			<ul class="swiper-wrapper">
				<? foreach ($arResult["ITEMS"] as $arItem): ?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<li class="swiper-slide" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
						<picture>
							<source loading="lazy" srcset="<?= $arItem["DESKTOP_IMG"] ?>" media="(min-width: 450px)" />
							<img loading="lazy" src="<?= $arItem["MOBILE_IMG"] ?>" alt="">
						</picture>
						<? if ($arItem['DISPLAY_PROPERTIES']['LINK']['VALUE']) { ?>
							<a href="<?= $arItem['DISPLAY_PROPERTIES']['LINK']['VALUE'] ?>"></a>
						<? } ?>
					</li>
				<? endforeach; ?>
			</ul>
		</div>
	</div>
<? endif; ?>