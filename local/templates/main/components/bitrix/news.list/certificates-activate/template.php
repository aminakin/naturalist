<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

global $userId;
?>
<div class="step-list">
	<?foreach ($arResult["ITEMS"] as $key => $arItem) {?>
		<div class="step-item">
			<div class="step-item-numder">
				<?=$key+1?>.
			</div> 
			<div class="step-item-text">
				<div class="step-item-title">
					<?=$arItem['DETAIL_TEXT']?> 
				</div>
				<div class="step-item-description">
					<?=$arItem['PREVIEW_TEXT']?> 
				</div>
			</div> 
		</div> 
	<?}?>
</div>  

<form action="POST" class="form-active-sert">
	<input type="hidden" name="user_id" value="<?=$userId?>">
	<div class="form-body">
		<div class="form-path" <?=$userId != 0 ? 'style="width: 0; max-width: inset"' : ''?>>
			<?if ($userId == 0) {?>
				<div class="form-input-wrap">
					<label for="phone">Введите номер телефона</label>
					<input required type="tel" name="phone" placeholder="+7 (___) ___-__-__">
				</div>
				<div class="form-input-wrap get-code-wrap">
					<button type="button" class="get-code">Получить проверочный код</button>
				</div>
				<div class="form-input-wrap code-input" style="display: none">				
					<input name="code" required type="text" placeholder="Введите полученный код">
					<a class="send-repeat">Отправить повторно </a>				
				</div>
			<?}?>
		</div>
		<div class="form-path">
			<div class="form-input-wrap">
				<label for="number">Введите номер сертификата </label>
				<div class="form-input-sertificat">
					<input required type="text" class="sertificate-number" name="number_1" placeholder="хххх" maxlength="4" size="4">
					<input required type="text" class="sertificate-number" name="number_1" placeholder="хххх" maxlength="4" size="4">
					<input required type="text" class="sertificate-number" name="number_2" placeholder="хххх" maxlength="4" size="4">
				</div>
				<p>вы сможете выбрать отель на ваши даты и сразу забронировать его</p>
			</div>
		</div>
	</div>
	<button type="submit" class="cert__activate">Активировать</button>
</form>

<div class="modal modal_form" id="cert__popup-wrap">
	<div class="modal__container">
		<div class="popup-sertification">
			<div class="popop-sert-text">
				<div class="popop-sert-title">Поздравляем!</div>
				<div class="popop-sert-desc">Сертификат<br>активирован,<br>на вашем счете</div>
				<div class="popop-sert-btn">
					<span>10000</span>
					<img src="<?=$templateFolder?>/img/curency.png" alt="curency.png">
				</div>
				<div class="popop-sert-date">Сертификат действителен<br>до <span>01.01.2024</span></div>
			</div>       
		</div>
	</div>
</div>

<script>
    let certActivate = new CertActivate();
</script>