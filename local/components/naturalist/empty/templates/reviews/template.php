<?
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
                        <div class="sidebar-navigation__label" data-navigation-control="data-navigation-control"><span>Мои отзывы</span></div>
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
                        <h1>Мои отзывы</h1>
                    </div>

                    <div class="profile__content">
                        <?if($arReviews):?>
                        <div class="profile-reviews">
                            <div class="profile-reviews__heading">
                                <div class="profile-reviews__number">№ заказа</div>
                                <div class="profile-reviews__content">Мой отзыв</div>
                            </div>

                            <div class="profile-reviews__list">
                                <?foreach($arReviews as $arItem):?>
                                <div class="profile-reviews__item" data-id="<?=$arItem["ID"]?>">
                                    <div class="profile-reviews__number"><?=$arItem["PROPERTIES"]["ORDER_ID"]["VALUE"]?></div>
                                    <div class="profile-reviews__content">
                                        <div class="profile-reviews__title">
                                            <div class="h6"><?=$arItem["NAME"]?></div>
                                            <div class="profile-reviews__date"><?=FormatDate("d F Y г.", strtotime($arItem["DATE_CREATE"]))?></div>
                                            <div class="profile-reviews__controls">
                                                <a href="#review" data-modal data-review-edit data-id="<?=$arItem["ID"]?>">Редактировать</a>
                                                <a href="#" data-review-delete data-id="<?=$arItem["ID"]?>">Удалить</a>
                                            </div>
                                        </div>

                                        <ul class="list list_stars">
                                            <?
                                            if (!empty($arItem["PROPERTIES"]["RATING"]["VALUE"])):
                                                for ($i = 1; $i <= 5; $i++):?>
                                                    <li class="list__item<? if ($i <= floor($arItem["PROPERTIES"]["RATING"]["VALUE"])):?> list__item_active<?endif; ?>">
                                                        <svg class="icon icon_star" viewbox="0 0 12 12"
                                                             style="width: 1.2rem; height: 1.2rem;">
                                                            <use xlink:href="#star"/>
                                                        </svg>
                                                    </li>
                                                <?endfor;
                                            endif;
                                            ?>
                                        </ul>

                                        <div class="profile-reviews__text">
                                            <p><?=$arItem["DETAIL_TEXT"]?></p>
                                        </div>

                                        <?if($arItem["PICTURES"]):?>
                                        <ul class="list list_upload">
                                            <?foreach($arItem["PICTURES"] as $arPhoto):?>
                                            <li class="list__item">
                                                <div class="list__item-image" style="background-image: url(<?=$arPhoto["SRC"]?>);"></div>
                                                <div class="list__item-content">
                                                    <div><?=$arPhoto["ORIGINAL_NAME"]?></div>
                                                    <span><?=CFile::FormatSize($arPhoto["FILE_SIZE"])?></span>
                                                    <button type="button" data-review-photo-delete data-id="<?=$arItem["ID"]?>" data-photo-id="<?=$arPhoto["ID"]?>">
                                                        <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                                                            <use xlink:href="#cross" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </li>
                                            <?endforeach;?>
                                        </ul>
                                        <?endif;?>
                                    </div>
                                </div>
                                <?endforeach;?>
                            </div>
                        </div>
                        <?else:?>
                        <div class="profile__empty">
                            <div class="profile__empty-title">Нет опубликованных отзывов</div>
                            <div class="profile__empty-text">
                                <p>Чтобы здесь появилась информация, оставьте отзыв к завершенному заказу.</p>
                                <p>Выбрать предложение можно в <a href="/catalog/">каталоге</a>.</p>
                            </div>
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

<div class="modal modal_review" id="review">
    <div class="modal__container">
        <button class="modal__close" data-modal-close="data-modal-close">
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="h3">Редактировать отзыв</div>

        <form class="form" id="form-review">
        </form>
    </div>
</div>