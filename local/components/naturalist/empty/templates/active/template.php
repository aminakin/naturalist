<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

foreach($arResult as $key => $value) {
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
                        <?if($arUser["PERSONAL_PHOTO"]):?>
                            <div class="profile-preview__image">
                                <img class="lazy" data-src="<?= $arUser["PERSONAL_PHOTO"]["src"] ?>" alt="<?= $arUser["NAME"] ?>">
                            </div>
                        <?endif;?>
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

                        <?if($arOrders || $orderNum):?>
                            <div class="profile__heading-controls">
                                <form class="form form_search" id="form-order-search">
                                    <div class="field">
                                        <input class="field__input" type="text" name="orderNum" value="<?=$orderNum?>" placeholder="Номер заказа">
                                        <button data-order-search>
                                            <svg class="icon icon_search" viewbox="0 0 16 17" style="width: 1.6rem; height: 1.7rem;">
                                                <use xlink:href="#search" />
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?endif;?>
                    </div>

                    <?if($arOrders):?>
                        <div class="sort">
                            <span>Сортировать по:</span>
                            <ul class="list">
                                <li class="list__item">
                                    <?if($sort == "date_create"):?>
                                        <span class="list__link">
                                    <span>По</span> <span>Дате бронирования</span>
                                </span>
                                    <?else:?>
                                        <a class="list__link" href="#" data-order-sort="date_create">
                                            <span>По</span> <span>Дате бронирования</span>
                                        </a>
                                    <?endif;?>
                                </li>
                                <li class="list__item">
                                    <?if($sort == "date_from"):?>
                                        <span class="list__link">
                                    <span>По</span> <span>Дате заезда</span>
                                </span>
                                    <?else:?>
                                        <a class="list__link" href="#" data-order-sort="date_from">
                                            <span>По</span> <span>Дате заезда</span>
                                        </a>
                                    <?endif;?>
                                </li>
                            </ul>
                        </div>
                    <?endif;?>

                    <div class="profile__content">
                        <?if($arOrders):?>
                            <?foreach($arOrders as $arOrder):?>
                                <?                                
                                $dateFrom = $arOrder["PROPS"]["DATE_FROM"];
                                $dateTo = $arOrder["PROPS"]["DATE_TO"];
                                $guests = $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["GUESTS_COUNT"]['VALUE'];
                                $daysCount = $daysCount = (strtotime($dateTo) - strtotime($dateFrom)) / (60*60*24);
                                $totalPrice = $arOrder["FIELDS"]["PRICE"];

                                $arOrderItem = $arOrder["ITEMS"][0]["ITEM"];
                                $arOrderSection = $arOrderItem["SECTION"];
                                if(!empty($arOrderSection["UF_PHOTOS"][0])){
                                    $photo = CFile::ResizeImageGet($arOrderSection["UF_PHOTOS"][0], array('width' => 600, 'height' => 400), BX_RESIZE_IMAGE_EXACT, true)["src"];
                                } else {
                                    $photo = SITE_TEMPLATE_PATH."/img/no_photo.png";
                                }
                                $alt = $arHLTypes[$arOrderSection["UF_TYPE"]]["UF_NAME"] . " " . $arOrderSection["NAME"];
                                $title = "Фото - " . $arOrderSection["NAME"];
                                ?>
                                <div class="object-row object-row_profile" data-id="<?=$arOrder["ID"]?>">
                                    <div class="object-row__images">
                                        <img class="lazy" alt="<?= $alt ?>" title="<?= $title ?>" data-src="<?=$photo?>">
                                    </div>

                                    <div class="object-row__content">
                                        <div class="object-row__description">
                                            <div class="object-row__headnote">Заказ №<?=$arOrder["FIELDS"]["ACCOUNT_NUMBER"]?></div>
                                            <div class="object-row__heading">
                                                <a class="object-row__title h3" href="<?=$arOrderSection["SECTION_PAGE_URL"]?>"><?=$arOrderSection["NAME"]?></a>
                                                <div class="score">
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt="Рейтинг">
                                                    <span><?=(int)$arOrderSection["RATING"]["avg"]?></span>
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
                                            <div class="object-row__text"><?=$arOrderItem["NAME"]?><?if($arOrderItem["PROPERTIES"]["SQUARE"]["VALUE"]):?>, <?=$arOrderItem["PROPERTIES"]["SQUARE"]["VALUE"]?> м²<?endif;?></div>
                                            <div class="object-row__text"><?=FormatDate("d F", strtotime($dateFrom))?> - <?=FormatDate("d F", strtotime($dateTo))?>, <?= $daysCount ?> <?= $daysDeclension->get($daysCount) ?>, <?=$guests?> <?= $guestsDeclension->get($guests) ?> <span>заезд с <?=$arOrderSection["UF_TIME_FROM"]?>, выезд до <?=$arOrderSection["UF_TIME_TO"]?></span></div>
                                        </div>

                                        <div class="object-row__order">
                                            <div class="object-row__price">
                                                <div><?= number_format($totalPrice, 0, '.', ' ') ?> ₽</div>
                                            </div>
                                            <div class="profile__status">
                                                <div class="tag"><?=$arOrder["DATA"]["STATUS"]?></div>
                                                <?if($arOrder["FIELDS"]["IS_PAYED"] == "Y"):?>
                                                    <a class="profile__get-vaucher" href="#" data-id="<?=$arOrder["ID"]?>"><?=Loc::getMessage('GET_VAUCHER')?></a>
                                                <?endif;?>
                                            </div>
                                                
                                            <?if($arOrder["FIELDS"]["IS_PAYED"] != "Y"):?>                                                
                                                <a class="button button_transparent" href="#" data-payment data-id="<?=$arOrder["ID"]?>">Оплатить</a>
                                            <?else:?>                                                
                                                <a class="button button_transparent" href="#" data-order-cancel data-id="<?=$arOrder["ID"]?>">Отмена</a>
                                            <?endif;?>
                                        </div>
                                    </div>
                                </div>
                            <?endforeach;?>

                        <?else:?>
                            <div class="profile__empty">
                                <div class="profile__empty-title">Нет активных заказов</div>
                                <div class="profile__empty-text">Начните планировать отдых с <a href="/catalog/">выбора глэмпинга</a> и забронируйте номер.</div>
                            </div>
                        <?endif;?>
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
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Отмена бронирования</div>

        <div class="modal__content">
            <p>На вашу электронную почту отправлено письмо с деталями отмены</p>
        </div>
    </div>
</div>