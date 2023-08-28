<?
/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

use Naturalist\Reviews;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
}

global $arUser, $isAuthorized;
if (!$isAuthorized) {
    exit;
}

$reviewId = $_REQUEST['reviewId'] ?? '';

$reviews = new Reviews();
$arReview = $reviews->get($reviewId);
$arPics = [];
if($arReview["PICTURES"]) {

    foreach($arReview["PICTURES"] as $arPhoto) {
        $arPics[] = array(
            "id" => $arPhoto["ID"],
            "image" => $arPhoto["SRC"],
            "name" => $arPhoto["ORIGINAL_NAME"],
            "size" => $arPhoto["FILE_SIZE"]
        );
    }
}
$picsJSON = htmlspecialchars(stripslashes(json_encode($arPics)));
?>
<form class="form" id="form-review">
    <input type="hidden" name="campingId" value="<?=$arReview["PROPERTIES"]["CAMPING_ID"]["VALUE"]?>">
    <input type="hidden" name="orderId" value="<?=$arReview["PROPERTIES"]["ORDER_ID"]["VALUE"]?>">

    <div class="form__item">
        <div class="field">
            <input class="field__input" type="text" name="name" placeholder="Имя" value="<?=$arReview["NAME"]?>">
            <span class="field__error" style="display: none;">Ошибка ввода</span>
        </div>
    </div>

    <div class="form__item">
        <div class="field">
            <textarea class="field__input" name="text" placeholder="Ваш отзыв"><?=$arReview["DETAIL_TEXT"]?></textarea>
        </div>
    </div>

    <div class="form__item">
        <div class="scores-edit">
            <div class="scores-edit__title">Ваша оценка:</div>
            <div class="scores-edit__list">
                <?for($i = 1; $i <= 8; $i++):?>
                <div class="scores-edit__item">
                    <div class="scores-edit__label"><?=$arReview["PROPERTIES"]["CRITERION_".$i]["NAME"]?></div>
                    <div class="scores-edit__value" data-rating="data-rating">
                        <input type="hidden" data-rating-field="data-rating-field" data-rating-field-num="<?=$i?>" value="<?=$arReview["PROPERTIES"]["CRITERION_".$i]["VALUE"]?>">
                        <ul class="list">
                            <?for($j = 5; $j >= 1; $j--):?>
                            <li class="list__item<?if($j <= $arReview["PROPERTIES"]["CRITERION_".$i]["VALUE"]):?> list__item_active<?endif;?>" data-rating-value="<?=$j?>"><span></span></li>
                            <?endfor;?>
                        </ul>
                    </div>
                </div>
                <?endfor;?>
            </div>
        </div>
    </div>

    <div class="form__item" data-dropzone-item="data-dropzone-item">
        <input class="dropzone-hide" type="file" name="files" multiple="multiple" value data-dropzone-value="data-dropzone-value" accept="image/*">
        <label class="dropzone" data-dropzone="<?=$picsJSON?>">
            <input type="file" multiple="multiple" value data-dropzone-add="data-dropzone-add" accept="image/*">
            <span class="dropzone__message">
                <svg class="icon icon_dropzone" viewbox="0 0 48 48" style="width: 4.8rem; height: 4.8rem;">
                    <use xlink:href="#dropzone" />
                </svg>
                <span>Перетащите фото сюда<br> или <strong>загрузите с компьютера</strong><br> до&nbsp;10&nbsp;файлов, максимальный размер 1&nbsp;файла&nbsp;-&nbsp;5&nbsp;мб</span>
            </span>
        </label>

        <ul class="list list_upload" data-dropzone-files="data-dropzone-files"></ul>
    </div>

    <div class="form__control">
        <button class="button button_primary" data-review-update data-id="<?=$arReview["ID"]?>">Сохранить</button>
    </div>
</form>