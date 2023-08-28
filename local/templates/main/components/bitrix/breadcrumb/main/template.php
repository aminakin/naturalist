<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$str = '';
foreach($arResult as $key => $arItem) {
    $str .= '<li class="list__item"><a class="list__link"'.(($key != count($arResult)-1) ? 'href="'.$arItem["LINK"].'"' : '').'>'.$arItem["TITLE"].'</a></li>';
}

return $str;