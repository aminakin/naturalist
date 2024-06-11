<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}
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

<section class="section section_title_page">
	<div class="container">
		<h1 class="page_title"><?= $arResult['h1SEO'] ?></h1>
	</div>
</section>


<?if ($arResult['IMP_SECTIONS']) {?>
	<section class="section section_impressions">
		<div class="container">
			<ul class="list list_impressions">
				<?foreach ($arResult['IMP_SECTIONS'] as $section) {?>
					<li class="list__item">
						<a class="list__link" href="<?=$arParams['FOLDER'].$section['CODE'].'/'?>">
							<div class="list__item-image">
								<div><img class="lazy" data-src="<?=CFile::getPath($section["PICTURE"])?>" alt="<?=$section["NAME"]?>" title="Фото - <?=$section["NAME"]?>"></div>
							</div>
							<div class="list__item-title"><?=$section["META"]['SECTION_PAGE_TITLE'] ? $section["META"]['SECTION_PAGE_TITLE'] : $section["NAME"]?></div>
						</a>
					</li>
				<?}?>
			</ul>
		</div>
	</section>
<?}
