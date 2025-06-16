<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$metaTags = getMetaTags();
$currentURLDir = $APPLICATION->GetCurDir();

if(!empty($metaTags[$currentURLDir])) {
    $APPLICATION->SetTitle($metaTags[$currentURLDir]["~PROPERTY_TITLE_VALUE"]["TEXT"]);
    $APPLICATION->AddHeadString('<meta name="description" content="'.$metaTags[$currentURLDir]["~PROPERTY_DESCRIPTION_VALUE"]["TEXT"].'" />');

} else {
    $APPLICATION->SetTitle("Объектам размещения - онлайн-сервис бронирования глэмпингов и кемпингов Натуралист");
    $APPLICATION->AddHeadString('<meta name="description" content="Объектам размещения | Натуралист - удобный онлайн-сервис поиска и бронирования глэмпинга для отдыха на природе с оплатой на сайте. Вы можете подобрать место для комфортного природного туризма в России по выгодным ценам с моментальной системой бронирования." />');
}
?>

    <main class="main">
        <section class="section section_crumbs">
            <div class="container">
                <div class="crumbs">
                    <ul class="list crumbs__list" itemscope itemtype="http://schema.org/BreadcrumbList">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:breadcrumb",
                            "main",
                            array(
                                "PATH" => "",
                                "SITE_ID" => "s1",
                                "START_FROM" => "0",
                                "COMPONENT_TEMPLATE" => "main"
                            ),
                            false
                        );
                        ?>
                    </ul>
                </div>
            </div>
        </section>
        <!-- section-->

        <section class="section section_add">
            <div class="container">
                <h1>Как подключить объект</h1>

                <div class="add-object">
                    <div class="h3">Заполните форму обратной связи</div>

                    <form class="form add-object__form form_validation" id="add-object-form">
                        <div class="form__item">
                            <div class="field">
                                <label class="field__label">Название объекта</label>
                                <input class="field__input" type="text" value name="title" placeholder="Название">
                            </div>
                        </div>
                        <div class="form__item">
                            <div class="field">
                                <label class="field__label">Сайт</label>
                                <input class="field__input" type="text" value name="site" placeholder="www.site.com">
                            </div>
                        </div>
                        <div class="form__item">
                            <div class="field">
                                <div class="dropdown" data-dropdown="radio">
                                    <input type="hidden" name="module" value data-dropdown-value data-module-reserv>
                                    <div class="field field_icon field_dropdown">
                                        <label class="field__label">Какой у вас модуль бронирования?</label>
                                        <div class="field__input" data-dropdown-label>Выберите модуль</div>
                                    </div>
                                    <div class="dropdown__values">
                                        <ul class="list">
                                            <li class="list__item" data-dropdown-id="traveline">Travelline</li>
                                            <li class="list__item" data-dropdown-id="bnovo">Bnovo</li>
                                            <li class="list__item" data-dropdown-id="other">Другой</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form__item" data-module-reserv-other>
                            <div class="field">
                                <input class="field__input" type="text" name="other" placeholder="Название другого модуля">
                            </div>
                        </div>
                        <div class="form__item">
                            <div class="field">
                                <label class="field__label">Контактное лицо</label>
                                <input class="field__input" type="text" value name="person" placeholder="ФИО">
                            </div>
                        </div>
                        <div class="form__item">
                            <div class="field">
                                <label class="field__label">E-mail</label>
                                <input class="field__input" type="text" value name="email" placeholder="mail@mail.com">
                            </div>
                        </div>
                        <div class="form__item">
                            <div class="field">
                                <label class="field__label">Контактный телефон</label>
                                <input class="field__input" type="tel" value name="phone" placeholder="+7 (___)-___-__-__">
                            </div>
                        </div>
                        <div class="form__item form__item_full-width">
                            <div class="field">
                                <label class="field__label">Комментарии</label>
                                <textarea name="message" class="field__input"></textarea>
                            </div>
                        </div>
                        <div class="form__item form__item_full-width">
                            <div class="field">
                                <label class="checkbox">
                                    <input type="checkbox" name="personal_data" value="1"><span>Я даю своё согласие на обработку указанных данных на условиях <a href="#">Политики конфиденциальности</a> в целях создания и обработки заявки и осуществления обратной связи по вопросам её рассмотрения.</span>
                                </label>
                            </div>
                        </div>
                        <div class="form__controls">
                            <button class="button button_blue" data-form-submit data-form-object-send>Отправить</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- section-->
    </main>
    <!-- main-->

    <script>

        $(function() {
            window.addEventListener('sendForm', event => {
                if(event.detail.form == 'add-object-form') {
                    var name = $('#add-object-form input[name="title"]').val();
                    var site = $('#add-object-form input[name="site"]').val();
                    var moduleName = $('#add-object-form input[name="module"]').val();
                    if(moduleName == 'other') {
                        moduleName = $('#add-object-form input[name="other"]').val();
                    }
                    if(!moduleName) {
                        window.infoModal(ERROR_TITLE, "Укажите модуль");
                        return;
                    }
                    var fio = $('#add-object-form input[name="person"]').val();
                    var email = $('#add-object-form input[name="email"]').val();
                    var phone = $('#add-object-form input[name="phone"]').val();
                    var message = $('#add-object-form textarea[name="message"]').val();

                    var data = {
                        name: name,
                        site: site,
                        module: moduleName,
                        fio: fio,
                        email: email,
                        phone: phone,
                        message: message
                    }

                    jQuery.ajax({
                        type: 'POST',
                        url: '/ajax/forms/object.php',
                        data: data,
                        dataType: 'json',
                        success: function(a) {
                            if(!a.ERROR) {
                                window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                                $("#add-object-form")[0].reset();
                                //location.reload();
                            } else {
                                window.infoModal(ERROR_TITLE, a.ERROR);
                            }
                        }
                    });
                }
            })
        });
    </script>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
