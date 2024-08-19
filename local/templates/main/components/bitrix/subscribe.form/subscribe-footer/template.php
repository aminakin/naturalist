<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
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
?>
<section class="subscribe-form" id="subscribe-form">
	<div class="container">
		<div class="subscribe-form__wrap">
			<p class="subscribe-form__title"><?=$arParams['~FORM_TITLE']?></p>
			<p class="subscribe-form__subtitle"><?=$arParams['FORM_SUBTITLE']?></p>
			<form action="<?=$arResult["FORM_ACTION"]?>">
				<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
					<label class="subscribe__label" for="sf_RUB_ID_<?=$itemValue["ID"]?>">
						<input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?=$itemValue["ID"]?>" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /> <?=$itemValue["NAME"]?>
					</label>
				<?endforeach;?>
				<input class="subscribe__input" type="text" name="sf_EMAIL" size="20" value="<?=$arResult["EMAIL"]?>" placeholder="<?=GetMessage("SUBSCRIBE_FORM_EMAIL_TITLE")?>"/>		
				<input class="button subscribe__submit" type="submit" name="OK" value="<?=GetMessage("SUBSCRIBE_FORM_BUTTON")?>"/></td>
				<span class="input__error"></span>
			</form>
			<p class="subscribe-form__agree"><?=GetMessage("SUBSCRIBE_FORM_POLITICS", Array ("#LINK#" => $arParams['FORM_POLITICS_LINK']))?></p>
		</div>
	</div>	
</section>
