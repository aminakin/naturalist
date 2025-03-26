<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<?
// echo "<pre>";
// print_r($arResult);
// echo "<pre>";
?>

<?
$styleClass = ["A", "B", "C", "D", "E", "F", "G"];
if(!empty($arResult)):?>
<div class="catalog_menu" id="catalog_menu">
    <div class="filters-attrs__block">
        <div class="form__dropdown form__dropdown_show box1">
            <?$count = 0;?>
            <?foreach($arResult as $key=>$arItem):?>
                <?if($key==0):?>
                    <div class="form__dropdown-heading_menu h6">
                            <?=$arItem["TEXT"]?>
                        </div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                <?elseif($arItem["LINK"] == "#"): $count = $count + 1?>
                                </ul>
                            </div>
                        </div>
                        <div class="form__dropdown form__dropdown_show box<?=$count+1?>">

                        <div class="form__dropdown-heading_menu h6">
                            <?=$arItem["TEXT"]?>
                        </div>
                        <div class="form__dropdown-body">
                            <ul class="list list_checkboxes">
                <?else:?>
                    <li class="list__item<?if($arItem["SELECTED"]):?> list__item_active<?endif;?>">
                        <a class="list__link <?if($arItem["PARAMS"]["ALWAYS_ORANGE"] == "Y"):?>always_orange<?endif;?>" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
                    </li>
                <?endif;?>
            <?endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?endif;?>