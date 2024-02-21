<?php 

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use DateTime;

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

                <?if (is_array($arOrders) && count($arOrders)) {?>
                    <div class="profile__article">
                        <div class="profile__heading">
                            <h1>Сертификаты</h1>
                        </div>

                        <div class="cert-order__list">                            
                            <? foreach ($arOrders as $arOrder): ?>
                                <?if (!$arOrder['UF_CODE']) {                                                                
                                    $dateFrom = $arOrder['FIELDS']["DATE_INSERT"]->format("d.m.Y");
                                    $newDate = new DateTime($dateFrom);                                
                                    $dateTo = $newDate->add(new DateInterval('P1Y'))->format("d.m.Y");                                
                                    ?>
                                    <div class="cert-order__item">
                                        <div class="cert-order__col-1">
                                            <img width="192" class="lazy" data-src="<?= $arOrder['PROPS']['CERT_FORMAT'] == 'fiz' ? $arOrder['PROPS']['FIZ_VARIANT'] : $arOrder['PROPS']['ELECTRO_VARIANT'] ?>">
                                        </div>

                                        <div class="cert-order__col-2">                                        
                                            <div class="cert-order__number">Заказ №<?= $arOrder["FIELDS"]["ACCOUNT_NUMBER"] ?></div>
                                            <span class="cert-order__prop">Дата покупки: <?=$dateFrom?></span>
                                            <span class="cert-order__prop">Срок действия: до <?=$dateTo?></span>
                                            <span class="cert-order__prop">Номинал: <?= number_format($arOrder['PROPS']['PROP_CERT_PRICE'], 0, '.', ' ') ?> ₽</span>
                                            <span class="cert-order__prop">Формат: <?= $arOrder['PROPS']['CERT_FORMAT'] == 'fiz' ? 'offline' : 'online'?></span>
                                            <span class="cert-order__prop">Адрес доставки: <?=$arOrder['PROPS']['CITY']?></span>
                                        </div>

                                        <div class="cert-order__col-3">
                                            <div class="cert-order__col-row">
                                                <span class="cert-order__sum-title">Общая сумма заказа</span>
                                                <div class="object-row__price">
                                                    <div><?= number_format($arOrder["FIELDS"]["PRICE"], 0, '.', ' ') ?> ₽</div>
                                                </div>
                                                <div class="tag"><?= $arOrder["DATA"]["STATUS"] ?></div>
                                                <a href="#" class="cart-order__get-pdf">Скачать сертификат</a>
                                            </div>
                                            <? if ($arOrder["FIELDS"]["STATUS_ID"] != "C") : ?>
                                                <button type="button" data-modal-review="<?= $arOrder["ID"] ?>"
                                                        data-before-review-add data-order-id="<?= $arOrder["ID"] ?>"
                                                        data-camping-id="<?= $arOrderSection["ID"] ?>">Оставить
                                                    отзыв</button>
                                            <? endif; ?>                               
                                        </div>                                    
                                    </div>
                                <? } else {?>
                                    <div class="cert-order__item cert">
                                        <div class="cert-order__col-1">
                                            <img width="192" class="lazy" data-src="<?= $arOrder['ORDER'][$arOrder['UF_ORDER_ID']]['PROPS']['CERT_FORMAT'] == 'fiz' ? $arOrder['ORDER'][$arOrder['UF_ORDER_ID']]['PROPS']['FIZ_VARIANT'] : $arOrder['ORDER']['PROPS']['ELECTRO_VARIANT'] ?>">
                                        </div>

                                        <div class="cert-order__col-2">                                        
                                            <div class="cert-order__number">Заказ №<?= $arOrder['ORDER'][$arOrder['UF_ORDER_ID']]["FIELDS"]["ACCOUNT_NUMBER"] ?></div>                                            
                                            <span class="cert-order__prop">Срок действия: до <?=$arOrder['UF_DATE_UNTIL']->format("d.m.Y")?></span>
                                            <a href="/catalog/" class="cert-order__to-catalog">Отправиться в путешествие</a>
                                        </div>

                                        <div class="cert-order__col-3">
                                            <div class="cert-order__col-row">
                                                <span class="cert-order__sum-title">Номинал<br> сертификата</span>
                                                <div class="object-row__price">
                                                    <div><?= number_format($arOrder['UF_COST'], 0, '.', ' ') ?> ₽</div>
                                                </div>
                                                <div class="tag"><?= $arOrder['ORDER'][$arOrder['UF_ORDER_ID']]["DATA"]["STATUS"] ?></div>
                                                <a href="#" class="cart-order__get-pdf">Скачать сертификат</a>
                                            </div>
                                            <? if ($arOrder["FIELDS"]["STATUS_ID"] != "C") : ?>
                                                <button type="button" data-modal-review="<?= $arOrder["ID"] ?>"
                                                        data-before-review-add data-order-id="<?= $arOrder["ID"] ?>"
                                                        data-camping-id="<?= $arOrderSection["ID"] ?>">Оставить
                                                    отзыв</button>
                                            <? endif; ?>                               
                                        </div>                                    
                                    </div>
                                <?}?>
                            <? endforeach; ?>                            
                        </div>
                    </div>
                <?} else {?>
                    <div class="profile__article">
                        <div class="profile__heading">
                            <p class="pers-cert__title">Как это работает</p>
                        </div>                    
                        <div class="pers-cert__steps">
                            <div class="pers-cert__step">
                                <div class="step__title">
                                    <span class="step__number">1</span>
                                    <span class="step__title-text"><?=$steps[0]['NAME']?></span>
                                </div>
                                <div class="step__text">
                                    <?=$steps[0]['PREVIEW_TEXT']?>
                                </div>
                            </div>
                            <div class="pers-cert__step">
                                <div class="step__title">
                                    <span class="step__number">2</span>
                                    <span class="step__title-text"><?=$steps[1]['NAME']?></span>
                                </div>
                                <div class="step__text">
                                    <?=$steps[1]['PREVIEW_TEXT']?>
                                </div>
                            </div>
                        </div>
                        <a href="/certificates/buy/" class="step__link orange">Купить</a>
                        <a class="step__link transparent" href="#corporat" data-modal>Для корпоративных клиентов</a>
                        <div class="pers-cert__step">
                            <div class="step__title">
                                <span class="step__number">3</span>
                                <span class="step__title-text"><?=$steps[2]['NAME']?></span>
                            </div>
                            <div class="step__text">
                                <?=$steps[2]['PREVIEW_TEXT']?>
                            </div>
                        </div>
                        <a href="/certificates/activate/" class="step__link blue">Активировать</a>
                    </div>
                <?}?>                
            </div>
        </div>
    </section>
    <!-- section-->
</main>
<!-- main-->

<?
    $APPLICATION->IncludeFile("/include/forms/corporat.php", [], []);
?>

<?
    $APPLICATION->IncludeFile("/include/forms/cert-review.php", [], []);
?>
