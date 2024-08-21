<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<?if(count($arResult["ITEMS"]) > 0):?>
    <div class="impressions-slider container">  
	<div class="impressions-slider__wrap">  
		<div class="impressions-slider__title">
			<span><?= Loc::getMessage('IMPRESSIONS_TITLE');?></span>
			<a href="/impressions/" class="all-link"><?= Loc::getMessage('IMPRESSIONS_WATCH_ALL');?></a>
		</div>
		<div class="swiper-container">
			<ul class="swiper-wrapper">
				<?foreach($arResult["ITEMS"] as $arItem):?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<li class="list__item swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<a class="list__link" href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>">
							<div class="list__item-image"style="background-image: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>')">
								
							</div>
							<div class="list__item-title"><?=$arItem["NAME"]?></div>
						</a>
					</li>
				<?endforeach;?>
			</ul>
			
		</div>
		<div class="swiper-button-prev">
			<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g filter="url(#filter0_b_3313_12381)">
				<rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M17.5752 14.0498C17.7949 14.2694 17.7949 14.6256 17.5752 14.8453L13.4205 19L17.5752 23.1548C17.7949 23.3744 17.7949 23.7306 17.5752 23.9503C17.3556 24.1699 16.9994 24.1699 16.7798 23.9503L12.2273 19.3978C12.1218 19.2923 12.0625 19.1492 12.0625 19C12.0625 18.8508 12.1218 18.7078 12.2273 18.6023L16.7798 14.0498C16.9994 13.8301 17.3556 13.8301 17.5752 14.0498Z" fill="white"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M12.1899 19C12.1899 18.6893 12.4418 18.4375 12.7524 18.4375H25.3749C25.6856 18.4375 25.9374 18.6893 25.9374 19C25.9374 19.3107 25.6856 19.5625 25.3749 19.5625H12.7524C12.4418 19.5625 12.1899 19.3107 12.1899 19Z" fill="white"/>
				</g>
				<defs>
				<filter id="filter0_b_3313_12381" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
				<feFlood flood-opacity="0" result="BackgroundImageFix"/>
				<feGaussianBlur in="BackgroundImageFix" stdDeviation="6"/>
				<feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_3313_12381"/>
				<feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_3313_12381" result="shape"/>
				</filter>
				</defs>
			</svg>
        </div>
        <div class="swiper-button-next">
			<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g filter="url(#filter0_b_3313_12375)">
				<rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M20.4245 14.0498C20.6442 13.8301 21.0003 13.8301 21.22 14.0498L25.7725 18.6023C25.878 18.7078 25.9373 18.8508 25.9373 19C25.9373 19.1492 25.878 19.2923 25.7725 19.3978L21.22 23.9503C21.0003 24.1699 20.6442 24.1699 20.4245 23.9503C20.2048 23.7306 20.2048 23.3744 20.4245 23.1548L24.5793 19L20.4245 14.8453C20.2048 14.6256 20.2048 14.2694 20.4245 14.0498Z" fill="white"/>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M12.0625 19C12.0625 18.6893 12.3143 18.4375 12.625 18.4375H25.2475C25.5582 18.4375 25.81 18.6893 25.81 19C25.81 19.3107 25.5582 19.5625 25.2475 19.5625H12.625C12.3143 19.5625 12.0625 19.3107 12.0625 19Z" fill="white"/>
				</g>
				<defs>
				<filter id="filter0_b_3313_12375" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
				<feFlood flood-opacity="0" result="BackgroundImageFix"/>
				<feGaussianBlur in="BackgroundImageFix" stdDeviation="6"/>
				<feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_3313_12375"/>
				<feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_3313_12375" result="shape"/>
				</filter>
				</defs>
			</svg>
        </div>
		</div>
		<a href="/impressions/" class="impressions-slider__more button"><?= Loc::getMessage('IMPRESSIONS_MORE');?></a>
	</div>
<?endif;?>
