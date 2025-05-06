<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}


$styleClass = ["A", "B", "C", "D", "E", "F", "G"];
if (!empty($arResult)):?>
    <div class="catalog_menu" id="catalog_menu">
        <div class="filters-attrs__block">
            <div class="form__dropdown form__dropdown_show box1">
                <?php $count = 0; ?>
                <?php foreach ($arResult

                as $key => $arItem): ?>
                <?php if ($key == 0): ?>
                <div class="form__dropdown-heading_menu h6">
                    <?= $arItem["TEXT"] ?>
                </div>
                <div class="form__dropdown-body">
                    <ul class="list list_checkboxes">
                        <?php elseif ($arItem["LINK"] == "#"):
                        $count = $count + 1 ?>
                    </ul>
                </div>
            </div>
            <div class="form__dropdown form__dropdown_show box<?= $count + 1 ?>">

                <div class="form__dropdown-heading_menu h6">
                    <?= $arItem["TEXT"] ?>
                </div>
                <div class="form__dropdown-body">
                    <ul class="list list_checkboxes">
                        <?php else: ?>
                            <li class="list__item<?php if ($arItem["SELECTED"]): ?> list__item_active<?php endif; ?>">
                                <a class="list__link <?php if ($arItem["PARAMS"]["ALWAYS_ORANGE"] == "Y"): ?>always_orange<?php endif; ?>"
                                   href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a>
                            </li>
                        <?php endif; ?>
                        <?php endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>