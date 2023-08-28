<?
global $arUser, $isAuthorized;
if (!$isAuthorized) {
    LocalRedirect('/');
}
?>

<main class="main">
    <section class="section section_crumbs">
        <div class="container">
            <div class="crumbs">
                <ul class="list crumbs__list">
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

    <section class="section section_favorite">
        <div class="container">
            <div class="profile">
                <div class="profile__sidebar">
                    <div class="profile-preview">
                        <?if($arUser["PERSONAL_PHOTO"]):?>
                            <div class="profile-preview__image">
                                <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>" alt="<?= $arUser["NAME"] ?>">
                            </div>
                        <?endif;?>
                        <div class="profile-preview__name"><?= $arUser["NAME"] ?></div>
                    </div>

                    <div class="sidebar-navigation">
                        <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span>Личные данные</span></div>
                        <ul class="list">
                            <?
                            $APPLICATION->IncludeComponent(
                                "bitrix:menu",
                                "footer",
                                array(
                                    "ROOT_MENU_TYPE" => "personal",
                                    "MAX_LEVEL" => "1",
                                    "CHILD_MENU_TYPE" => "",
                                    "USE_EXT" => "N",
                                    "DELAY" => "N",
                                    "ALLOW_MULTI_SELECT" => "Y",
                                    "MENU_CACHE_TYPE" => "N",
                                    "MENU_CACHE_TIME" => "3600",
                                    "MENU_CACHE_USE_GROUPS" => "Y",
                                    "MENU_CACHE_GET_VARS" => ""
                                ),
                                false
                            );
                            ?>
                        </ul>
                    </div>
                    <a class="button button_transparent" href="#feedback" data-modal>Связаться с нами</a>
                </div>

                <div class="profile__article">
                    <div class="profile__heading">
                        <h1>Личные данные</h1>
                    </div>

                    <div class="profile__content">
                        <div class="person">
                            <div class="person__item">
                                <div class="person__heading">
                                    <div class="h3">Аккаунт</div>
                                </div>
                                <div class="person__body">
                                    <div class="person__data">
                                        <div class="person__data-item">
                                            <div class="person__data-label">ФИО</div>
                                            <div class="person__data-value">
                                                <? if (!empty($arUser["LAST_NAME"]) || !empty($arUser["NAME"]) || !empty($arUser["SECOND_NAME"])) : ?>
                                                    <span><?= $arUser["LAST_NAME"] ?> <?= $arUser["NAME"] ?> <?= $arUser["SECOND_NAME"] ?></span>
                                                <? endif; ?>
                                                <a href="#name-edit" data-modal><? if (!empty($arUser["NAME"]) || !empty($arUser["LAST_NAME"]) || !empty($arUser["SECOND_NAME"])) : ?>Изменить<? else : ?>Ввести данные<? endif; ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="person__data">
                                        <div class="person__data-item">
                                            <div class="person__data-label">E-mail</div>
                                            <div class="person__data-value">
                                                <? if (!empty($arUser["EMAIL"])) : ?>
                                                    <span><?= $arUser["EMAIL"] ?></span>
                                                <? endif; ?>
                                                <a href="#email-edit" data-modal><? if (!empty($arUser["EMAIL"])) : ?>Изменить<? else : ?>Добавить почту<? endif; ?></a>
                                            </div>
                                        </div>

                                        <div class="person__data-item">
                                            <div class="person__data-label">Телефон</div>
                                            <div class="person__data-value">
                                                <? if (!empty($arUser["PERSONAL_PHONE"])) : ?>
                                                    <span><?= $arUser["PERSONAL_PHONE"] ?></span>
                                                <? endif; ?>
                                                <a href="#phone-edit" data-modal><? if (!empty($arUser["PERSONAL_PHONE"])) : ?>Изменить<? else : ?>Добавить телефон<? endif; ?></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="person__data">
                                        <div class="person__data-item">
                                            <div class="person__data-label">Аватар</div>
                                            <div class="person__data-value">
                                                <div class="person__data-avatar">
                                                    <? if (!empty($arUser["PERSONAL_PHOTO"])) : ?>
                                                        <span><?=$arUser["PERSONAL_PHOTO"]["ORIGINAL_NAME"]?></span>
                                                        <a class="person__data-avatar-control" href="#photo-edit" data-modal>
                                                            <svg class="icon icon_phone" viewbox="0 0 20 20" style="width: 2rem; height: 2rem;">
                                                                <use xlink:href="#phone" />
                                                            </svg>
                                                        </a>
                                                        <button class="person__data-avatar-control" data-avatar-remove>
                                                            <svg class="icon icon_remove" viewbox="0 0 20 20" style="width: 2rem; height: 2rem;">
                                                                <use xlink:href="#remove" />
                                                            </svg>
                                                        </button>
                                                    <? else : ?>
                                                        <a class="person__data-avatar-control" href="#photo-edit" data-modal>
                                                            <svg class="icon icon_phone" viewbox="0 0 20 20" style="width: 2rem; height: 2rem;">
                                                                <use xlink:href="#phone" />
                                                            </svg>
                                                        </a>
                                                    <? endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="person__item">
                                <div class="person__heading">
                                    <div class="h3">Уведомления по почте</div>
                                </div>

                                <div class="person__body">
                                    <div class="person__trigger">
                                        <div class="person__trigger-label">
                                            <div>Изменения в бронях</div>
                                            <span>Важные изменения по вашим броням</span>
                                        </div>
                                        <label class="person__trigger-checkbox">
                                            <input type="checkbox" name="subscribe-email-1" data-field-update <?if($arUser["UF_SUBSCRIBE_EMAIL_1"]):?>checked<?endif;?> >
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <?if(false):?>
                                <div class="person__item">
                                    <div class="person__heading">
                                        <div class="h3">Уведомления по СМС</div>
                                    </div>
                                    <div class="person__body">
                                        <div class="person__trigger">
                                            <div class="person__trigger-label">
                                                <div>Изменения в бронях</div>
                                                <span>Важные изменения по вашим броням</span>
                                            </div>
                                            <label class="person__trigger-checkbox">
                                                <input type="checkbox" name="subscribe-sms-1" data-field-update <?if($arUser["UF_SUBSCRIBE_SMS_1"]):?>checked<?endif;?>>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

<div class="modal modal_form modal_edit" id="name-edit">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Аккаунт</div>

        <form class="form" id="form-name-save">
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="surname" <?if(!empty($arUser["LAST_NAME"])):?>value="<?=$arUser["LAST_NAME"]?>"<?endif;?> placeholder="Фамилия">
                </div>
            </div>
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="name" <?if(!empty($arUser["NAME"])):?>value="<?=$arUser["NAME"]?>"<?endif;?> placeholder="Имя">
                </div>
            </div>
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="lastname" <?if(!empty($arUser["SECOND_NAME"])):?>value="<?=$arUser["SECOND_NAME"]?>"<?endif;?> placeholder="Отчество">
                </div>
            </div>

            <div class="form__controls">
                <button class="button button_primary" data-name-save>Сохранить</button>
                <button class="button button_transparent" data-modal-close="data-modal-close">Отменить</button>
            </div>
        </form>
    </div>
</div>

<div class="modal modal_form modal_edit" id="phone-edit">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Добавить телефон</div>
        <div class="modal__footnote">На введённый номер придет СМС с кодом для подтверждения привязки</div>

        <form class="form" id="form-phone-save">
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="tel" name="phone" placeholder="+7 (___) ___-__-__">
                </div>
            </div>
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="code" placeholder="Код из СМС">
                </div>
            </div>
            <div class="form__item form__item_code">
                <a href="#" data-phone-code-send>Отправить код</a>
            </div>

            <div class="form__controls">
                <button class="button button_primary" data-phone-save>Привязать номер</button>
            </div>
        </form>
    </div>
</div>

<div class="modal modal_form modal_edit" id="email-edit">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Добавить почту</div>
        <div class="modal__footnote">На указанную почту придет письмо с&nbsp;кодом для подтверждения привязки</div>

        <form class="form" id="form-email-save">
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="email" placeholder="mail@mail.com">
                </div>
            </div>
            <div class="form__item">
                <div class="field">
                    <input class="field__input" type="text" name="code" placeholder="Код из письма">
                </div>
            </div>
            <div class="form__item form__item_code">
                <a href="#" data-email-code-send>Отправить код</a>
            </div>

            <div class="form__controls">
                <button class="button button_primary" data-email-save>Привязать почту</button>
            </div>
        </form>
    </div>
</div>

<div class="modal modal_photo" id="photo-edit">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>

        <form class="form" id="form-photo-save">
            <div class="form__item">
                <div class="avatar" data-avatar="data-avatar">
                    <div class="avatar__dropzone">
                        <label class="dropzone" data-dropzone-avatar="data-dropzone-avatar">
                            <input type="file" name="avatar" value data-dropzone-avatar-value="data-dropzone-avatar-value" accept="image/*">
                            <span class="dropzone__message">
                                <svg class="icon icon_dropzone" viewbox="0 0 48 48" style="width: 4.8rem; height: 4.8rem;">
                                    <use xlink:href="#dropzone" />
                                </svg>
                                <span>Перетащите фото сюда<br> или <strong>загрузите с компьютера</strong>,<br> максимальный размер 1&nbsp;файла&nbsp;-&nbsp;5&nbsp;мб</span>
                            </span>
                        </label>
                    </div>

                    <div class="avatar__crop">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==" alt data-crop-avatar="{&quot;minCropBoxWidth&quot;: 276}">
                    </div>
                </div>
            </div>

            <div class="form__controls">
                <button class="button button_primary" data-avatar-save="data-avatar-save">Сохранить</button>
                <button class="button button_transparent" data-modal-close="data-modal-close">Отменить</button>
            </div>
        </form>
    </div>
</div>