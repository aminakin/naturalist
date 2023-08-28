<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<?if(!empty($arResult)):?>
    <?foreach($arResult as $arItem):?>
    <li class="list__item<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>">
        <a class="list__link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
    </li>
    <?endforeach;?>
<?endif;?>