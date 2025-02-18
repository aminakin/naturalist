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
                                                <use xlink:href="#search" />
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
                                    $photo = CFile::ResizeImageGet(
                                        $arOrderSection["UF_PHOTOS"][0],
                                        array('width' => 600, 'height' => 400),
                                        BX_RESIZE_IMAGE_EXACT,
                                        true
                                    )["src"];
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
                                                    <button type="button" data-modal-review="<?= $arOrder["ID"] ?>"
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

<?
$APPLICATION->IncludeComponent(
    "naturalist:reviews.add",
    "",
    [],
    false
);
?>