<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<?
// echo "<pre>";
// print_r($arResult);
// echo "<pre>";
?>
<?if(!empty($arResult)):?>
    <ul class="list">
        <? $newblock = 1;?>
        <?foreach($arResult as $key=>$arItem):?>
            <?if($key==0):?>
                <h2><?=$arItem["TEXT"]?></h2>
            <?elseif($arItem["LINK"] == "#"):?>
                </ul>
                <ul class="list">
                <h2><?=$arItem["TEXT"]?></h2>
            <?else:?>
                <li class="list__item<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>">
                    <a class="list__link <?if($arItem["PARAMS"]["ALWAYS_ORANGE"] == "Y"):?>always_orange<?endif;?>" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                </li>
            <?endif;?>
        <?endforeach;?>
    </ul>
<?endif;?>