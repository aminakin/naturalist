<?
foreach ($arResult as $key => $value) {
    ${$key} = $value;
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
                        <? if ($arUser["PERSONAL_PHOTO"]): ?>
                            <div class="profile-preview__image">
                                <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>"
                                     alt="<?= $arUser["NAME"] ?>" title="Фото - <?= $arUser["NAME"] ?>">
                            </div>
                        <? endif; ?>
                        <div class="profile-preview__name"><?= $arUser["NAME"] ?></div>
                    </div>

                    <div class="sidebar-navigation">
                        <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span>История путешествий</span>
                        </div>
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
                        <h1>История путешествий</h1>

                        <? if ($arOrders || $orderNum): ?>
                            <div class="profile__heading-controls">
                                <form class="form form_search" id="form-order-search">
                                    <div class="field">
                                        <input class="field__input" type="text" name="orderNum" value="<?= $orderNum ?>"
                                               placeholder="Номер заказа">
                                        <button data-order-search>
                                            <svg class="icon icon_search" viewbox="0 0 16 17"
                                                 style="width: 1.6rem; height: 1.7rem;">
                                                <use xlink:href="#search"/>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <? endif; ?>
                    </div>

                    <div class="sort">
                        <ul class="list">
                            <li class="list__item">
                                <? if ($status == "F"): ?>
                                    <span class="list__link">Завершённые</span>
                                <? else: ?>
                                    <a class="list__link" href="#" data-status="F">Завершённые</a>
                                <? endif; ?>
                            </li>
                            <li class="list__item">
                                <? if ($status == "C"): ?>
                                    <span class="list__link">Отменённые</span>
                                <? else: ?>
                                    <a class="list__link" href="#" data-status="C">Отменённые</a>
                                <? endif; ?>
                            </li>
                        </ul>
                    </div>

                    <div class="profile__content">
                        <? if ($arOrders): ?>
                            <? foreach ($arOrders as $arOrder): ?>
                                <?
                                $dateFrom = $arOrder["PROPS"]["DATE_FROM"];
                                $dateTo = $arOrder["PROPS"]["DATE_TO"];
                                $guests = $arOrder["PROPS"]["GUESTS_COUNT"];
                                $daysCount = (strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24);
                                $totalPrice = $arOrder["FIELDS"]["PRICE"];

                                $arOrderItem = $arOrder["ITEMS"][0]["ITEM"];
                                $arOrderSection = $arOrderItem["SECTION"];
                                if (!empty($arOrderSection["UF_PHOTOS"][0])) {
                                    $photo = CFile::ResizeImageGet($arOrderSection["UF_PHOTOS"][0],
                                        array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true)["src"];
                                } else {
                                    $photo = SITE_TEMPLATE_PATH . "/img/no_photo.png";
                                }
                                $alt = $arHLTypes[$arOrderSection["UF_TYPE"]]["UF_NAME"] . " " . $arOrderSection["NAME"];
                                $title = "Фото - " . $arOrderSection["NAME"];
                                ?>
                                <div class="object-row object-row_profile" data-id="<?= $arOrder["ID"] ?>">
                                    <div class="object-row__images">
                                        <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                             data-src="<?= $photo ?>">
                                    </div>

                                    <div class="object-row__content">
                                        <div class="object-row__description">
                                            <div class="object-row__headnote">Заказ
                                                №<?= $arOrder["FIELDS"]["ACCOUNT_NUMBER"] ?>
                                                <? if ($arOrder["FIELDS"]["STATUS_ID"] != "C") : ?>
                                                    <button type="button"data-modal-review="<?= $arOrder["ID"] ?>"
                                                            data-before-review-add data-order-id="<?= $arOrder["ID"] ?>"
                                                            data-camping-id="<?= $arOrderSection["ID"] ?>">Оставить
                                                        отзыв</button><? endif; ?>
                                            </div>
                                            <div class="object-row__heading">
                                                <a class="object-row__title h3"
                                                   href="<?= $arOrderSection["SECTION_PAGE_URL"] ?>"><?= $arOrderSection["NAME"] ?></a>
                                                <div class="score">
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg"
                                                         alt="Рейтинг">
                                                    <span><?= (int)$arOrderSection["RATING"]["avg"] ?></span>
                                                </div>
                                            </div>
                                            <div class="area-info">
                                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt="Маркер">
                                                <div>
                                                    <? if (isset($arHLTypes[$arOrderSection["UF_TYPE"]])) : ?>
                                                        <span><?= $arHLTypes[$arOrderSection["UF_TYPE"]]["UF_NAME"] ?></span><? endif; ?>
                                                    <? if (!empty($arOrderSection["UF_DISTANCE"])) : ?>
                                                        <span><?= $arOrderSection["UF_DISTANCE"] ?></span><? endif; ?>
                                                    <? if (!empty($arOrderSection["UF_ADDRESS"])) : ?>
                                                        <span><?= $arOrderSection["UF_ADDRESS"] ?></span><? endif; ?>
                                                </div>
                                            </div>
                                            <div class="object-row__text"><?= $arOrderItem["NAME"] ?><? if ($arOrderItem["PROPERTIES"]["SQUARE"]["VALUE"]): ?>, <?= $arOrderItem["PROPERTIES"]["SQUARE"]["VALUE"] ?> м²<? endif; ?></div>
                                            <div class="object-row__text"><?= FormatDate("d F", strtotime($dateFrom)) ?>
                                                - <?= FormatDate("d F", strtotime($dateTo)) ?>
                                                , <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?>
                                                , <?= $guests ?> <?= $guestsDeclension->get($guests) ?>
                                                <span>заезд с <?= $arOrderSection["UF_TIME_FROM"] ?? "14:00" ?>, выезд до <?= $arOrderSection["UF_TIME_TO"] ?? "12:00" ?></span>
                                            </div>
                                        </div>

                                        <div class="object-row__order">
                                            <div class="object-row__price">
                                                <div><?= number_format($totalPrice, 0, '.', ' ') ?> ₽</div>
                                            </div>
                                            <div class="tag"><?= $arOrder["DATA"]["STATUS"] ?></div>
                                            <a class="button button_transparent"
                                               href="<?= $arOrderSection["SECTION_PAGE_URL"] ?>">Повторить</a>
                                        </div>
                                    </div>
                                </div>
                            <? endforeach; ?>

                        <? else: ?>
                            <div class="profile__empty">
                                <div class="profile__empty-title">Нет завершенных заказов</div>
                                <div class="profile__empty-text">
                                    <p>Чтобы здесь появилась история, завершите хотя бы один заказ.</p>
                                    <p>Выбрать предложение можно в <a href="/catalog/">каталоге</a>.</p>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

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
                    <div class="scores-edit__title">Ваша оценка:</div>
                    <div class="scores-edit__list">
                        <? for ($i = 1; $i <= 8; $i++): ?>
                            <div class="scores-edit__item">
                                <div class="scores-edit__label"><?= $arProps["CRITERION_" . $i] ?></div>
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