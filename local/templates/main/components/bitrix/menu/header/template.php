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
		    <li class="list__item <?if($arItem["TEXT"] == "Каталог"): echo'list__item_catalog'; endif;?>
					<?if($arItem["PARAMS"]["IS_MOBILE"] == "Y"):?>list__item_mobile<?endif;?> 
					<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>
					"
					<?if($arItem["TEXT"] == "Каталог"): echo'id="list__item_catalog"'; endif;?>
			>
		        <a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>

		        <ul class="list list_sub">
		<?else:?>
			<?if($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="list__item <?if($arItem["TEXT"] == "Каталог"): echo'list__item_catalog'; endif;?>
						<?if($arItem["PARAMS"]["IS_MOBILE"] == "Y"):?>list__item_mobile<?endif;?> 
						<?if($arItem["PARAMS"]["HIGHLIGHT_BROWN"] == "Y"):?>highlight_orange<?endif;?>
						<?if($arItem["PARAMS"]["ALWAYS_ORANGE"] == "Y"):?>always_orange<?endif;?>
						<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>
					"
					<?if($arItem["TEXT"] == "Каталог"): echo'id="list__item_catalog"'; endif;?>
				>
					<a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
				</li>
		    <?else:?>
				<li class="list__item <?if($arItem["TEXT"] == "Каталог"): echo'list__item_catalog'; endif;?>
						<?if($arItem["PARAMS"]["IS_MOBILE"] == "Y"):?>list__item_mobile<?endif;?> 
						<?if($arItem["PARAMS"]["HIGHLIGHT_BROWN"] == "Y"):?>highlight_orange<?endif;?> 
						<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>
					"
					<?if($arItem["TEXT"] == "Каталог"): echo'id="list__item_catalog"'; endif;?>
				>
					<a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
				</li>
		    <?endif;?>
		<?endif;?>

		<?
		$previousLevel = $arItem["DEPTH_LEVEL"];
		?>
	<?endforeach;?>


    <?
    $menuItems = [];

    foreach ($arResult as $item) {
        $menuItems[] = [
            "@type" => "SiteNavigationElement",
            "name" => $item["TEXT"],
            "url" => $item["LINK"]
        ];
    }

    $menuSchema = [
        "@context" => "https://schema.org",
        "@graph" => $menuItems
    ];
    ?>

    <script type="application/ld+json">
    <?= json_encode($menuSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
    </script>


    
<?endif;?>