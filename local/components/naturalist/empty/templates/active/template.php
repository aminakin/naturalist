<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

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
                                <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>" alt="<?= $arUser["NAME"] ?>">
                            </div>
                        <? endif; ?>
                        <div class="profile-preview__name"><?= $arUser["NAME"] ?></div>
                    </div>

                    <div class="sidebar-navigation">
                        <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span>Активные заказы</span></div>
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
                        <h1>Активные заказы</h1>

                        <? if ($arOrders || $orderNum): ?>
                            <div class="profile__heading-controls">
                                <form class="form form_search" id="form-order-search">
                                    <div class="field">
                                        <input class="field__input" type="text" name="orderNum" value="<?= $orderNum ?>" placeholder="Номер заказа">
                                        <button data-order-search>
                                            <svg class="icon icon_search" viewbox="0 0 16 17" style="width: 1.6rem; height: 1.7rem;">
                                                <use xlink:href="#search" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <? endif; ?>
                    </div>

                    <? if ($arOrders): ?>
                        <div class="sort">
                            <span>Сортировать по:</span>
                            <ul class="list">
                                <li class="list__item">
                                    <? if ($sort == "date_create"): ?>
                                        <span class="list__link">
                                            <span>По</span> <span>Дате бронирования</span>
                                        </span>
                                    <? else: ?>
                                        <a class="list__link" href="#" data-order-sort="date_create">
                                            <span>По</span> <span>Дате бронирования</span>
                                        </a>
                                    <? endif; ?>
                                </li>
                                <li class="list__item">
                                    <? if ($sort == "date_from"): ?>
                                        <span class="list__link">
                                            <span>По</span> <span>Дате заезда</span>
                                        </span>
                                    <? else: ?>
                                        <a class="list__link" href="#" data-order-sort="date_from">
                                            <span>По</span> <span>Дате заезда</span>
                                        </a>
                                    <? endif; ?>
                                </li>
                            </ul>
                        </div>
                    <? endif; ?>

                    <div class="profile__content">
                        <? if ($arOrders): ?>
                            <? foreach ($arOrders as $arOrder): ?>
                                <?
                                $dateFrom = $arOrder["PROPS"]["DATE_FROM"];
                                $dateTo = $arOrder["PROPS"]["DATE_TO"];
                                $guests = $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["GUESTS_COUNT"]['VALUE'];
                                $daysCount = $daysCount = (strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24);
                                $totalPrice = $arOrder["FIELDS"]["PRICE"];

                                $arOrderItem = $arOrder["ITEMS"][0]["ITEM"];
                                $arOrderSection = $arOrderItem["SECTION"];
                                if (!empty($arOrderSection["UF_PHOTOS"][0])) {
                                    $photo = CFile::ResizeImageGet($arOrderSection["UF_PHOTOS"][0], array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true)["src"];
                                } else {
                                    $photo = SITE_TEMPLATE_PATH . "/img/no_photo.png";
                                }
                                $alt = $arHLTypes[$arOrderSection["UF_TYPE"]]["UF_NAME"] . " " . $arOrderSection["NAME"];
                                $title = "Фото - " . $arOrderSection["NAME"];
                                ?>
                                <div class="object-row object-row_profile" data-id="<?= $arOrder["ID"] ?>">
                                    <div class="object-row__images">
                                        <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?= $photo ?>">
                                    </div>

                                    <div class="object-row__content">
                                        <div class="object-row__description">
                                            <div class="object-row__headnote">Заказ №<?= $arOrder["FIELDS"]["ACCOUNT_NUMBER"] ?></div>
                                            <div class="object-row__heading">
                                                <a class="object-row__title h3" href="<?= $arOrderSection["SECTION_PAGE_URL"] ?>"><?= $arOrderSection["NAME"] ?></a>
                                                <div class="score">
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt="Рейтинг">
                                                    <span><?= (int)$arOrderSection["RATING"]["avg"] ?></span>
                                                </div>
                                            </div>
                                            <div class="area-info">
                                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt="Маркер">
                                                <div>
                                                    <? if (isset($arHLTypes[$arOrderSection["UF_TYPE"]])) : ?><span><?= $arHLTypes[$arOrderSection["UF_TYPE"]]["UF_NAME"] ?></span><? endif; ?>
                                                    <? if (!empty($arOrderSection["UF_DISTANCE"])) : ?><span><?= $arOrderSection["UF_DISTANCE"] ?></span><? endif; ?>
                                                    <? if (!empty($arOrderSection["UF_ADDRESS"])) : ?><span><?= $arOrderSection["UF_ADDRESS"] ?></span><? endif; ?>
                                                </div>
                                            </div>
                                            <div class="object-row__text"><?= $arOrderItem["NAME"] ?><? if ($arOrderItem["PROPERTIES"]["SQUARE"]["VALUE"]): ?>, <?= $arOrderItem["PROPERTIES"]["SQUARE"]["VALUE"] ?> м²<? endif; ?></div>
                                            <div class="object-row__text"><?= FormatDate("d F", strtotime($dateFrom)) ?> - <?= FormatDate("d F", strtotime($dateTo)) ?>, <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?>, <?= $guests ?> <?= $guestsDeclension->get($guests) ?> <span>заезд с <?= $arOrderSection["UF_TIME_FROM"] ?>, выезд до <?= $arOrderSection["UF_TIME_TO"] ?></span></div>
                                        </div>

                                        <?php if (is_array($arOrder['CANCEL_INFO']) && !empty($arOrder['CANCEL_INFO'])) : ?>
                                            <div class="room__cancelation mobile">
                                                <div class="room__cancelation-tooltip">
                                                    <div class="room__cancelation-tooltip-title">Условия отмены бронирования</div>
                                                    <ul>
                                                        <?php foreach ($arOrder['CANCEL_INFO'] as $info) : ?>
                                                            <li><?= $info ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="5" viewBox="0 0 10 5" fill="none">
                                                        <path d="M9.5 0L5 5L0.5 0H9.5Z" fill="#E0C695"></path>
                                                    </svg>
                                                </div>
                                                <div class="room__cancelation-title">
                                                    Условия отмены
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                        <path d="M9 17.0625C4.5525 17.0625 0.9375 13.4475 0.9375 9C0.9375 4.5525 4.5525 0.9375 9 0.9375C13.4475 0.9375 17.0625 4.5525 17.0625 9C17.0625 13.4475 13.4475 17.0625 9 17.0625ZM9 2.0625C5.175 2.0625 2.0625 5.175 2.0625 9C2.0625 12.825 5.175 15.9375 9 15.9375C12.825 15.9375 15.9375 12.825 15.9375 9C15.9375 5.175 12.825 2.0625 9 2.0625Z" fill="black"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.5625C8.43899 6.5625 8.0625 6.97212 8.0625 7.38462C8.0625 7.69528 7.81066 7.94712 7.5 7.94712C7.18934 7.94712 6.9375 7.69528 6.9375 7.38462C6.9375 6.26771 7.90415 5.4375 9 5.4375C10.0958 5.4375 11.0625 6.26771 11.0625 7.38462C11.0625 7.78173 10.9362 8.14981 10.7238 8.45453C10.5926 8.64269 10.4397 8.82172 10.3 8.98201C10.2743 9.01149 10.2491 9.04031 10.2243 9.06858C10.1083 9.2011 10.0026 9.32194 9.90482 9.44595C9.66069 9.75567 9.5625 9.97137 9.5625 10.1538V10.5C9.5625 10.8107 9.31066 11.0625 9 11.0625C8.68934 11.0625 8.4375 10.8107 8.4375 10.5V10.1538C8.4375 9.57162 8.74634 9.09836 9.0213 8.74953C9.13876 8.60052 9.26748 8.45354 9.38384 8.32067C9.40711 8.29411 9.42987 8.26812 9.45196 8.24278C9.59095 8.08333 9.70791 7.94456 9.80089 7.81118C9.88929 7.68437 9.9375 7.53879 9.9375 7.38462C9.9375 6.97212 9.56101 6.5625 9 6.5625Z" fill="black"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.4375 12.375C8.4375 12.0643 8.68934 11.8125 9 11.8125H9.00674C9.3174 11.8125 9.56924 12.0643 9.56924 12.375C9.56924 12.6857 9.3174 12.9375 9.00674 12.9375H9C8.68934 12.9375 8.4375 12.6857 8.4375 12.375Z" fill="black"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="object-row__order">
                                            <div class="object-row__price">
                                                <div><?= number_format((float)$totalPrice, 0, '.', ' ') ?> ₽</div>
                                            </div>
                                            <div class="profile__status">
                                                <div class="tag"><?= $arOrder["DATA"]["STATUS"] ?></div>
                                                <? if ($arOrder["FIELDS"]["IS_PAYED"] == "Y"): ?>
                                                    <a class="profile__get-vaucher" href="#" data-id="<?= $arOrder["ID"] ?>"><?= Loc::getMessage('GET_VAUCHER') ?></a>
                                                <? endif; ?>
                                            </div>

                                            <? if ($arOrder["FIELDS"]["IS_PAYED"] != "Y"): ?>
                                                <a class="button button_transparent" href="#" data-payment data-id="<?= $arOrder["ID"] ?>">Оплатить</a>
                                            <? else: ?>
                                                <?php if (is_array($arOrder['CANCEL_INFO']) && !empty($arOrder['CANCEL_INFO'])) : ?>
                                                    <div class="room__cancelation">
                                                        <div class="room__cancelation-title">
                                                            Условия отмены
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                                <path d="M9 17.0625C4.5525 17.0625 0.9375 13.4475 0.9375 9C0.9375 4.5525 4.5525 0.9375 9 0.9375C13.4475 0.9375 17.0625 4.5525 17.0625 9C17.0625 13.4475 13.4475 17.0625 9 17.0625ZM9 2.0625C5.175 2.0625 2.0625 5.175 2.0625 9C2.0625 12.825 5.175 15.9375 9 15.9375C12.825 15.9375 15.9375 12.825 15.9375 9C15.9375 5.175 12.825 2.0625 9 2.0625Z" fill="black"></path>
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6.5625C8.43899 6.5625 8.0625 6.97212 8.0625 7.38462C8.0625 7.69528 7.81066 7.94712 7.5 7.94712C7.18934 7.94712 6.9375 7.69528 6.9375 7.38462C6.9375 6.26771 7.90415 5.4375 9 5.4375C10.0958 5.4375 11.0625 6.26771 11.0625 7.38462C11.0625 7.78173 10.9362 8.14981 10.7238 8.45453C10.5926 8.64269 10.4397 8.82172 10.3 8.98201C10.2743 9.01149 10.2491 9.04031 10.2243 9.06858C10.1083 9.2011 10.0026 9.32194 9.90482 9.44595C9.66069 9.75567 9.5625 9.97137 9.5625 10.1538V10.5C9.5625 10.8107 9.31066 11.0625 9 11.0625C8.68934 11.0625 8.4375 10.8107 8.4375 10.5V10.1538C8.4375 9.57162 8.74634 9.09836 9.0213 8.74953C9.13876 8.60052 9.26748 8.45354 9.38384 8.32067C9.40711 8.29411 9.42987 8.26812 9.45196 8.24278C9.59095 8.08333 9.70791 7.94456 9.80089 7.81118C9.88929 7.68437 9.9375 7.53879 9.9375 7.38462C9.9375 6.97212 9.56101 6.5625 9 6.5625Z" fill="black"></path>
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.4375 12.375C8.4375 12.0643 8.68934 11.8125 9 11.8125H9.00674C9.3174 11.8125 9.56924 12.0643 9.56924 12.375C9.56924 12.6857 9.3174 12.9375 9.00674 12.9375H9C8.68934 12.9375 8.4375 12.6857 8.4375 12.375Z" fill="black"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="room__cancelation-tooltip">
                                                            <div class="room__cancelation-tooltip-title">Условия отмены бронирования</div>
                                                            <ul>
                                                                <?php foreach ($arOrder['CANCEL_INFO'] as $info) : ?>
                                                                    <li><?= $info ?></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="5" viewBox="0 0 10 5" fill="none">
                                                                <path d="M9.5 0L5 5L0.5 0H9.5Z" fill="#E0C695"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <a class="button button_transparent" href="#" data-order-cancel data-id="<?= $arOrder["ID"] ?>">Отмена</a>
                                            <? endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <? endforeach; ?>

                        <? else: ?>
                            <div class="profile__empty">
                                <div class="profile__empty-title">Нет активных заказов</div>
                                <div class="profile__empty-text">Начните планировать отдых с <a href="/catalog/">выбора глэмпинга</a> и забронируйте номер.</div>
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

<div class="modal modal_cancel" id="cancel">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Условия отмены бронирования</div>

        <div class="modal__content">
            <ul class="list">
                <li class="list__item">За 14 суток до заезда возвращается 100% от стоимости бронирования.</li>
                <li class="list__item">Менее чем за 14 суток до заезда возвращается 50% от стоимости бронирования.</li>
                <li class="list__item">От 7 до 3 суток до заезда возвращается 25% от стоимости бронировании.</li>
                <li class="list__item">Менее 3х суток до даты заезда предоплата не возвращается.</li>
                <li class="list__item">Если гость освобождает номер за 1 или более дней до окончания срока проживания сумма оплаты, внесенная ранее, не возвращается.</li>
            </ul>

            <div class="modal__content-control">
                <a href="/cancel/">Подробнее</a>
            </div>

            <div class="modal__content-control">
                <label class="checkbox">
                    <input type="checkbox" name="terms">
                    <span>Согласен с условиями</span>
                </label>
            </div>

            <div class="modal__content-control">
                <button class="button button_primary" data-order-cancel data-order-id="">Отменить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_cancel" id="cancel-done">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <span>Скрыть</span>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Бронь отменена</div>

        <div class="modal__content">
            <p>Нам важно ваше мнение. Почему передумали?</p>
            <?php if (!empty($reasonCancel)) : ?>
                <div class="reason-items">
                    <?php foreach ($reasonCancel as $key => $reason) : ?>
                        <div class="reason-item checkbox">
                            <input <?= $key == 0 ? 'checked' : '' ?> data-id="<?= $reason['ID'] ?>" type="radio" id="<?= $reason['XML_ID'] ?>" name="radio">
                            <label for="<?= $reason['XML_ID'] ?>"><?= $reason['VALUE'] ?></label>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-input" style="display: none;">
                        <input type="text" id="free-text" placeholder="Напишите свой вариант">
                    </div>
                </div>
            <?php endif; ?>
            <div data-id="" data-modal-add-reason class="modal__content-btn cancel button button_primary">
                Отправить
            </div>
        </div>
    </div>
</div>

<div class="modal modal_cancel" id="cancel-step">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <span>Скрыть</span>
            <svg class="icon icon_cross" viewbox="0 0 15 15" style="width: 15px; height: 15px;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__content">
            <div class="modal__content-title">Вы уверены, что хотите отменить бронирование?</div>
            <div class="modal__content-info"></div>
            <div class="modal__content-nochange">Это действие нельзя будет отменить.</div>
            <div class="modal__content-btns">
                <div data-modal-close class="modal__content-btn return button button_primary">
                    Вернуться к заказу
                </div>
                <div data-id="" data-modal-cancel-order class="modal__content-btn cancel button button_primary">
                    Да отменить
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_canceled">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <span>Скрыть</span>
            <svg class="icon icon_cross" viewbox="0 0 15 15" style="width: 15px; height: 15px;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__content">
            <div class="modal__content-title">Бронь отменена</div>
            <div class="modal__content-info">Нам важно ваше мнение. Почему передумали?</div>
            <div class="modal__content-why">
                <label for="nofound">
                    <input type="radio" id="nofound" name="why" value="nofound">        
                    <span>Нашёл другое место</span>
                </label>
                
                <label for="change">
                    <input type="radio" id="change" name="why" value="change">
                    <span>Изменились планы</span>
                </label>
                
                <label for="nosuit">
                    <input type="radio" id="nosuit" name="why" value="nosuit">
                    <span>Не устроили условия проживания</span>
                </label>
                
                <label for="problem">
                    <input type="radio" id="problem" name="why" value="problem">
                    <span>Проблемы с сайтом или оплатой</span>
                </label>
                
                <label for="ownoption">
                    <input type="radio" id="ownoption" name="why" value="ownoption">
                    <span>Свой вариант</span>
                </label>

                <div class="ownoption-input">
                    <input type="text" id="ownoption-input" name="why" value="">
                </div>
            </div>
            <div class="modal__content-btns">
                <div class="modal__content-btn cancel button button_primary">
                    Да отменить
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_canceled" id="cancel-finish">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <span>Скрыть</span>
            <svg class="icon icon_cross" viewbox="0 0 15 15" style="width: 15px; height: 15px;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__content">
            <div class="modal__content-title">Спасибо!</div>
            <div class="modal__content-info">Ваш ответ поможет нам стать лучше</div>
        </div>
    </div>
</div>