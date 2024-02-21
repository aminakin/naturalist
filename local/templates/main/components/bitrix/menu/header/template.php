<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<?if(!empty($arResult)):?>
	<?
	$previousLevel = 0;
	foreach($arResult as $arItem):?>
		<?if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
			<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
		<?endif;?>

		<?if($arItem["IS_PARENT"]):?>
		    <li class="list__item 
					<?if($arItem["PARAMS"]["IS_MOBILE"] == "Y"):?>list__item_mobile<?endif;?> 
					<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>
					"
			>
		        <a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>

		        <ul class="list list_sub">
		<?else:?>
			<?if($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="list__item 
						<?if($arItem["PARAMS"]["IS_MOBILE"] == "Y"):?>list__item_mobile<?endif;?> 
						<?if($arItem["PARAMS"]["HIGHLIGHT_BROWN"] == "Y"):?>highlight_orange<?endif;?>
						<?if($arItem["PARAMS"]["ALWAYS_ORANGE"] == "Y"):?>always_orange<?endif;?>
						<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>
					"
				>
					<a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
				</li>
		    <?else:?>
				<li class="list__item 
						<?if($arItem["PARAMS"]["IS_MOBILE"] == "Y"):?>list__item_mobile<?endif;?> 
						<?if($arItem["PARAMS"]["HIGHLIGHT_BROWN"] == "Y"):?>highlight_orange<?endif;?> 
						<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>
					"
				>
					<a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
				</li>
		    <?endif;?>
		<?endif;?>

		<?
		$previousLevel = $arItem["DEPTH_LEVEL"];
		?>
	<?endforeach;?>
<?endif;?>