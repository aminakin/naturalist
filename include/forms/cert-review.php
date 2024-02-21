<div class="modal modal_review" id="review">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross"/>
            </svg>
        </button>
        <div class="h3">Оставить отзыв</div>
        <div class="modal__footnote">Именно благодаря вашим отзывам мы растём и развиваемся</div>

        <form class="form" id="review-form">
            <input type="hidden" value data-object-review-id="data-object-review-id">
            <input type="hidden" name="campingId" value="">
            <input type="hidden" name="orderId" value="">

            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="name" placeholder="Имя">
                    <span class="field__error" style="display: none;">Ошибка ввода</span>
                </div>
            </div>

            <div class="form__item">
                <div class="field">
                    <textarea class="field__input" name="text" placeholder="Ваш отзыв"></textarea>
                </div>
            </div>

            <div class="form__item">
                <div class="scores-edit">                    
                    <div class="scores-edit__list">                        
                        <div class="scores-edit__item">
                            <div class="scores-edit__label"><b>Ваша оценка:</b></div>
                            <div class="scores-edit__value" data-rating="data-rating">
                                <input type="hidden" data-rating-field="data-rating-field" name="RATING"
                                        data-rating-field-num="1" value="0">
                                <ul class="list">
                                    <? for ($j = 5; $j >= 1; $j--): ?>
                                        <li class="list__item" data-rating-value="<?= $j ?>"><span></span></li>
                                    <? endfor; ?>
                                </ul>
                            </div>
                        </div>                        
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
                        <svg class="icon icon_dropzone" viewbox="0 0 48 48" style="width: 4.8rem; height: 4.8rem;">
                          <use xlink:href="#dropzone"/>
                        </svg>
                        <span>Перетащите фото сюда<br> или <strong>загрузите с компьютера</strong><br> до&nbsp;10&nbsp;файлов, максимальный размер 1&nbsp;файла&nbsp;-&nbsp;5&nbsp;мб</span>
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