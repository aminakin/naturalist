<div class="modal modal_form" id="corporat">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>        
        <div class="modal__footnote"><span>Сертификат на загородный отдых</span> - идеальный подарок для коллег, партнеров и клиентов! Оставьте ваши контакты и мы молниеносно с вами свяжемся ;)</div>

        <form class="form form_validation" id="form-corporat">
            <div class="form__item">
                <span>Как к вам обращаться?</span>
                <div class="field">
                    <input class="field__input" type="text" name="name" placeholder="Имя">
                </div>
            </div>
            <div class="form__item">
                <span>Ваш телефон</span>
                <div class="field">
                    <input class="field__input" type="tel" name="phone" placeholder="Телефон">
                </div>
            </div>
            <div class="form__item">
                <span>E-mail</span>
                <div class="field">
                    <input class="field__input" type="text" name="email" placeholder="E-mail">
                </div>
            </div>            
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="cancel_policy" value="1">
                    <span>Я даю своё согласие на обработку указанных данных на условиях <a href="/agreement/">Политики конфиденциальности</a> в целях создания и обработки заявки и осуществления обратной связи по вопросам её рассмотрения.</span>
                </label>
            </div>            
            <div class="form__controls">
                <button class="button" data-form-corporat-send data-form-submit>Отправить</button>
            </div>
        </form>
    </div>
</div>