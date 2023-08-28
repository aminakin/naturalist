<?
foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>

<div class="reservation">
    <div class="reservation__content">
        <div class="reservation__heading">
            <div class="reservation__heading-title">
                <h1 class="h3"><?= $arSection["NAME"] ?></h1>
                <div class="area-info">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt>
                    <div>
                        <? if (isset($arHLTypes[$arSection["UF_TYPE"]])) : ?><span><?= $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] ?></span><? endif; ?>
                        <? if (!empty($arSection["UF_DISTANCE"])) : ?><span><?= $arSection["UF_DISTANCE"] ?></span><? endif; ?>
                        <? if (!empty($arSection["UF_ADDRESS"])) : ?><span><?= $arSection["UF_ADDRESS"] ?></span><? endif; ?>
                    </div>
                </div>
            </div>

            <div class="reservation__heading-score">
                <div class="score">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
                    <span><?= $avgRating ?></span>
                </div>
                <a href="<?= $arSection["SECTION_PAGE_URL"] ?>"><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?></a>
            </div>
        </div>

        <ul class="list list_time">
            <li class="list__item"><span>Заезд</span>
                <div>с <?=$arSection["UF_TIME_FROM"]?></div>
            </li>
            <li class="list__item"><span>Выезд</span>
                <div>до <?=$arSection["UF_TIME_TO"]?></div>
            </li>
        </ul>

        <ul class="list list_icon">
            <li class="list__item">
                <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/5.svg" alt></div>
                <div class="list__item-title"><?=$arElement["NAME"]?></div>
            </li>
            <li class="list__item">
                <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/1.svg" alt></div>
                <div class="list__item-title"><?=FormatDate("d F", strtotime($dateFrom))?> - <?=FormatDate("d F", strtotime($dateTo))?>, <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?></div>
            </li>
            <li class="list__item">
                <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/3.svg" alt></div>
                <div class="list__item-title"><?= $guests ?> <?= $guestsDeclension->get($guests) ?></div>
            </li>
            <?if($children > 0):?>
                <li class="list__item">
                    <div class="list__item-icon"><img src="<?=SITE_TEMPLATE_PATH?>/assets/img/reservation/3.svg" alt></div>
                    <div class="list__item-title"><?= $children ?> <?= $childrenDeclension->get($children) ?></div>
                </li>
            <?endif;?>

            <?foreach($arSection["UF_FEATURES"] as $featureId):?>
                <?
                $arIcon = CFile::GetFileArray($arHLFeatures[$featureId]["UF_ICON"]);
                ?>
                <li class="list__item">
                    <div class="list__item-icon">
                        <img src="<?=$arIcon["SRC"]?>" alt="<?=$arHLFeatures[$featureId]["UF_NAME"]?>">
                    </div>
                    <div class="list__item-title"><?=$arHLFeatures[$featureId]["UF_NAME"]?></div>
                </li>
            <?endforeach;?>
        </ul>
    </div>

    <div class="reservation__image">
        <img class="lazy" data-src="<?=current($arSection["PICTURES"])["src"]?>" alt="<?=$arSection["NAME"]?>">
    </div>
</div>

<form class="form reservation-form form_validation" id="form-order">
    <div class="reservation-form__form">
        <div class="reservation-form__form-holder">
            <?if(!$isAuthorized):?>
                <div class="h3">Для продолжения бронирования, пожалуйста, авторизируйтесь</div>
                <div class="reservation-form__control">
                    <a class="button button_transparent" href="#login-email" data-modal>Авторизация</a>
                </div>
            <?else:?>
            <?//xprint($arResult);?>
                <div class="reservation-form__fields">
                    <div class="reservation-form__fields-item">
                        <div class="form__row">
                            <div class="field">
                                <input class="field__input" name="email" type="email" placeholder="user@mail.ru" value="<?=$arUser["EMAIL"]?>">
                            </div>

                            <div class="field">
                                <input class="field__input" name="phone" type="text" placeholder="+7 (999) 999-99-99" value="<?=$arUser["PERSONAL_PHONE"]?>">
                            </div>

                            <input type="hidden" name="date_from" value="<?=$dateFrom?>" />
                            <input type="hidden" name="date_to" value="<?=$dateTo?>" />
                            <input type="hidden" name="guests" value="<?=$guests?>" />
                            <input type="hidden" name="childrenAge" value="<?=$childrenAge?>" />
                            <input type="hidden" name="service" value="<?=$arSection["UF_EXTERNAL_SERVICE"]?>" />
                            <input type="hidden" name="sectionId" value="<?=$arSection["ID"]?>" />
                            <input type="hidden" name="elementId" value="<?=$arElement["ID"]?>" />
                            <input type="hidden" name="externalId" value="<?=$arSection["UF_EXTERNAL_ID"]?>" />
                            <input type="hidden" name="externalElementId" value="<?=$arElement["PROPERTY_EXTERNAL_ID_VALUE"]?>" />
                            <input type="hidden" name="price" value="<?=$totalPrice?>" />
                            <?if($arSection["UF_EXTERNAL_SERVICE"] == 1):?>
                                <input type="hidden" name="travelineCategoryId" value="<?=$arElement["PROPERTY_EXTERNAL_CATEGORY_ID_VALUE"]?>" />
                                <input type="hidden" name="travelineChecksum" value="<?=$checksum?>" />
                            <?else:?>
                                <input type="hidden" name="tariffId" value="<?=$tariffValue?>" />
                                <input type="hidden" name="priceOneNight" value="<?=$priceOneNight?>" />
                                <input type="hidden" name="categoryId" value="<?=$categoryId?>" />
                            <?endif;?>
                        </div>
                        <span class="form__footnote">На этот адрес и телефон мы отправим подтверждение о бронировании</span>
                    </div>

                    <?for($i = 1; $i <= $guests; $i++):?>
                        <div class="reservation-form__fields-item" data-guest-row="<?=$i?>" data-check-disabled>
                            <div class="reservation-form__fields-label"><?=($i <= 5) ? $arGuestsNamesData[$i] : 'Дополнительный'?> гость</div>

                            <div class="form__row form__row_3" data-autocomplete>
                                <div class="field">
                                    <input class="field__input" type="text" name="surname" value="<?=$arUser["UF_GUESTS_DATA"][$i]["surname"] ?? ''?>" data-autocomplete-field="last" placeholder="Фамилия">
                                </div>
                                <div class="field">
                                    <input class="field__input" type="text" name="name" value="<?=$arUser["UF_GUESTS_DATA"][$i]["name"] ?? ''?>" data-autocomplete-field="first" placeholder="Имя">
                                </div>
                                <div class="field">
                                    <input class="field__input" type="text" name="lastname" value="<?=$arUser["UF_GUESTS_DATA"][$i]["lastname"] ?? ''?>" data-autocomplete-field="middle" placeholder="Отчество">
                                </div>

                                <ul class="list list_autocomplete" data-autocomplete-list></ul>
                            </div>

                            <label class="checkbox">
                                <input type="checkbox" name="save"
                                       <?if($arUser["UF_GUESTS_DATA"][$i]):?>checked<?endif;?>
                                       value
                                       <?if(!$arUser["UF_GUESTS_DATA"][$i]):?>disabled<?endif;?>
                                       data-check-disabled-control
                                >
                                <span>Сохранить данные гостя для будущих бронирований</span>
                            </label>
                        </div>
                    <?endfor;?>

                    <div class="reservation-form__fields-item">
                        <div class="reservation-form__fields-label">Комментарий к заказу</div>
                        <div class="field">
                            <textarea class="field__input" name="comment" placeholder="Введите дополнительную информацию или пожелания к заказу"></textarea>
                        </div>
                    </div>
                </div>
            <?endif;?>
        </div>
    </div>

    <div class="reservation-form__summary">
        <div class="reservation-form__summary-holder">
            <ul class="list list_summary_time">
                <li class="list__item">
                    <div class="h3">Заезд</div>
                    <span><?=FormatDate("d F Y, D", strtotime($dateFrom))?></span>
                    <span>с <?=$arSection["UF_TIME_FROM"]?></span>
                </li>
                <li class="list__item">
                    <div class="h3">Выезд</div>
                    <span><?=FormatDate("d F Y, D", strtotime($dateTo))?></span>
                    <span>до <?=$arSection["UF_TIME_TO"]?></span>
                </li>
            </ul>

            <div class="reservation-form__price">
                <span>Стоимость</span>
                <div class="h1"><?= number_format($totalPrice, 0, '.', ' ') ?> ₽</div>
            </div>
            <? if ($isAuthorized): ?>
            <button class="button button_primary" type="button" data-form-submit <?if($isAuthorized):?>data-order<?endif;?>>Оплатить банковской картой</button>
            <div class="reservation-form__footnote">
                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" name="personal_data" value="1">
                        <span>Согласен с условиями <a href="/agreement/">пользовательского соглашения</a>.</span>
                    </label>
                </div>
                <?if($arSection["UF_EXTERNAL_SERVICE"] == 1):?>
                    <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" name="cancel_policy" value="1">
                            <span>Ознакомлен с <a href="#" data-get-cancellation-amount>условиями отмены бронирования</a>.</span>
                        </label>
                    </div>
                <?else:?>
                    <div class="field">
                        <label class="checkbox">
                            <input type="checkbox" name="cancel_policy" value="1">
                            <span>Ознакомлен с <a href="#" data-get-cancellation-amount-bnovo>условиями отмены бронирования</a>.</span>
                        </label>
                    </div>
                <?endif;?>
                <div>Передача информации защищена сертификатом SSL, оплата осуществляется через интернет-эквайринг СБЕР.</div>
            </div>
            <? endif; ?>
        </div>
    </div>
</form>

<div class="modal modal_cancel" id="cancel">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Условия отмены бронирования</div>

        <div class="modal__content">
            <ul class="list" data-resevation-list-free style="display: none;">
                <li class="list__item reservation-date">Бесплатная отмена до <span></span> (Московское время).</li>
                <li class="list__item reservation-penalty">Далее штраф за отмену бронирования - <span></span>.</li>
            </ul>
            <ul class="list" data-resevation-list style="display: none;">
                <li class="list__item reservation-penalty">Штраф за отмену бронирования - <span></span>.</li>
            </ul>

            <!--<div class="modal__content-control">
                <a target="_blank" href="/cancel/">Подробнее</a>
            </div>-->

            <div class="modal__content-control">
                <button class="button button_primary order_cancel_button" data-modal-close>Принять</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal_cancel" id="cancelBnovo">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Условия отмены бронирования</div>

        <div class="modal__content">
            <ul class="list" data-resevation-list-free style="display: none;">
                <li class="list__item reservation-date">Бесплатная отмена до <span></span> (Московское время).</li>
                <li class="list__item reservation-penalty">Штраф за отмену бронирования - <span></span> ₽.</li>
            </ul>
            <ul class="list" data-resevation-list style="display: none;">
                <li class="list__item reservation-penalty">Штраф за отмену бронирования - <span></span> ₽.</li>
            </ul>

            <div class="modal__content-control">
                <button class="button button_primary order_cancel_button" data-modal-close>Принять</button>
            </div>
        </div>
    </div>
</div>

<script>
    const autocomplete = [
        <?foreach($arUser["UF_GUESTS_DATA"] as $key => $arItem):?>
        {
            id: '<?=$key?>',
            last: '<?=$arItem["surname"]?>',
            first: '<?=$arItem["name"]?>',
            middle: '<?=$arItem["lastname"]?>'
        },
        <?endforeach;?>
    ];
</script>