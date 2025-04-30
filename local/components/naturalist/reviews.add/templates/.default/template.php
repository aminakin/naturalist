<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

global $arUser, $userId;
?>

<div class="modal modal_review" id="review">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal_review__title">Оставить отзыв</div>
        <div class="modal__footnote">Именно благодаря вашим отзывам мы растём и развиваемся</div>

        <form class="form" id="review-form">
            <input type="hidden" value data-object-review-id="data-object-review-id">
            <input type="hidden" name="campingId" value="">
            <input type="hidden" name="orderId" value="">

            <div class="form__item">
                <div class="field">


                        <span class="review-modal__label">Имя</span>
                        <input class="field__input" type="text" name="name" placeholder="Введите ваше имя" <? if($arUser['NAME']){ ?> readonly <? } ?>>
                    <? if($arUser['NAME']): ?>
                        <span class="name-field" style="display:none"><?=$arUser['NAME']?></span>
                    <? endif ?>

                    <span class="field__error" style="display: none;">Ошибка ввода</span>
                </div>
            </div>

            <div class="form__item">
                <div class="field">
                    <span class="review-modal__label">Ваш отзыв</span>
                    <textarea class="field__input" name="text" placeholder="Опишите свои впечателния"></textarea>
                </div>
            </div>

            <div class="form__item">
                <div class="scores-edit">
                    <div class="scores-edit__title">Ваша оценка:</div>
                    <div class="scores-edit__list">
                        <? for ($i = 1; $i <= 8; $i++): ?>
                            <div class="scores-edit__item">
                                <div class="scores-edit__label"><?= $arResult["CRITERION_" . $i] ?></div>
                                <div class="scores-edit__value" data-rating="data-rating">
                                    <input type="hidden" data-rating-field="data-rating-field"
                                        data-rating-field-num="<?= $i ?>" value="0">
                                    <ul class="list">
                                        <? for ($j = 5; $j >= 1; $j--): ?>
                                            <li class="list__item" data-rating-value="<?= $j ?>"><span></span></li>
                                        <? endfor; ?>
                                    </ul>
                                </div>
                            </div>
                        <? endfor; ?>
                    </div>
                </div>
            </div>

            <div class="form__item" data-dropzone-item="data-dropzone-item">
                <input class="dropzone-hide" name="files" type="file" multiple="multiple" value
                    data-dropzone-value="data-dropzone-value" accept="image/*">
                <label class="dropzone" data-dropzone="[]">
                    <input type="file" multiple="multiple" value="" data-dropzone-add="data-dropzone-add"
                        accept="image/*">
                    <span class="dropzone__message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="41" height="41" viewBox="0 0 41 41" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M30.5003 2.1665C31.4208 2.1665 32.167 2.9127 32.167 3.83317V8.83317H37.167C38.0875 8.83317 38.8337 9.57936 38.8337 10.4998C38.8337 11.4203 38.0875 12.1665 37.167 12.1665H32.167V17.1665C32.167 18.087 31.4208 18.8332 30.5003 18.8332C29.5799 18.8332 28.8337 18.087 28.8337 17.1665V12.1665H23.8337C22.9132 12.1665 22.167 11.4203 22.167 10.4998C22.167 9.57936 22.9132 8.83317 23.8337 8.83317H28.8337V3.83317C28.8337 2.9127 29.5799 2.1665 30.5003 2.1665Z" fill="#E39250" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5429 3.8335L19.667 3.8335C20.5875 3.8335 21.3337 4.57969 21.3337 5.50016C21.3337 6.42064 20.5875 7.16683 19.667 7.16683C15.8879 7.16683 13.2213 7.17037 11.2023 7.44181C9.23133 7.70681 8.129 8.19933 7.33091 8.99741C6.53282 9.7955 6.0403 10.8978 5.77531 12.8688C5.50387 14.8878 5.50033 17.5544 5.50033 21.3335C5.50033 25.1126 5.50387 27.7792 5.77531 29.7982C6.0403 31.7692 6.53282 32.8715 7.33091 33.6696C8.129 34.4677 9.23133 34.9602 11.2023 35.2252C13.2213 35.4966 15.8879 35.5002 19.667 35.5002C23.4461 35.5002 26.1127 35.4966 28.1317 35.2252C30.1027 34.9602 31.205 34.4677 32.0031 33.6696C32.8012 32.8715 33.2937 31.7692 33.5587 29.7982C33.8301 27.7792 33.8337 25.1126 33.8337 21.3335V20.5002C33.8337 19.5797 34.5799 18.8335 35.5003 18.8335C36.4208 18.8335 37.167 19.5797 37.167 20.5002V21.4575C37.167 25.0842 37.1671 27.9754 36.8623 30.2423C36.5476 32.5829 35.8808 34.506 34.3601 36.0266C32.8395 37.5473 30.9164 38.2141 28.5758 38.5288C26.3089 38.8336 23.4177 38.8335 19.791 38.8335H19.543C15.9163 38.8335 13.0251 38.8336 10.7582 38.5288C8.41755 38.2141 6.49454 37.5473 4.97389 36.0266C3.45324 34.506 2.78639 32.5829 2.4717 30.2423C2.16692 27.9754 2.16695 25.0842 2.16699 21.4576V21.2094C2.16695 17.5828 2.16692 14.6916 2.4717 12.4247C2.78639 10.0841 3.45324 8.16104 4.97389 6.64039C6.49454 5.11974 8.41755 4.45289 10.7582 4.1382C13.0251 3.83343 15.9163 3.83346 19.5429 3.8335Z" fill="#E39250" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M6.85985 22.4185C11.5482 22.3227 16.1311 23.7841 19.792 26.5581C23.1727 29.1197 25.569 32.6516 26.6108 36.6153C26.7071 36.9818 26.632 37.3723 26.4065 37.6768C26.181 37.9814 25.8295 38.1673 25.4508 38.1821C23.7866 38.2474 21.8488 38.2474 19.6063 38.2474H19.49H19.4899C15.8388 38.2474 12.971 38.2474 10.732 37.9464C8.43756 37.6379 6.61714 36.9929 5.18681 35.5625C3.9437 34.3194 3.28818 32.7945 2.93506 30.9021C2.58923 29.0488 2.5181 26.7513 2.50197 23.8969C2.49845 23.2732 2.95512 22.7424 3.57228 22.6528C4.65972 22.4948 5.7589 22.4165 6.85985 22.4185ZM24.6997 27.761C24.3174 27.3147 24.4671 26.614 25.0266 26.4344C26.4553 25.9757 27.9183 25.742 29.3983 25.7475C31.5919 25.7457 33.7565 26.2756 35.8287 27.2982C36.2856 27.5237 36.56 28.004 36.522 28.5121C36.2965 31.5351 35.7124 33.8347 33.9846 35.5625C32.6808 36.8663 31.0529 37.5176 29.0373 37.8563C29.1919 37.2476 29.1932 36.6022 29.0296 35.9798C28.232 32.9453 26.7427 30.1462 24.6997 27.761Z" fill="#E39250" />
                        </svg>
                        <span>Перетащите фото сюда или <strong>загрузите с компьютера</strong><br> до&nbsp;10&nbsp;файлов, максимальный размер 1&nbsp;файла&nbsp;-&nbsp;5&nbsp;мб</span>
                    </span>
                </label>
                <ul class="list list_upload" data-dropzone-files="data-dropzone-files"></ul>
            </div>

            <div class="form__control">
                <button class="button button_primary" data-review-add>Отправить</button>
            </div>
        </form>
    </div>
</div>