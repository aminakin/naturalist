<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

foreach ($arResult as $key => $value) {
    ${$key} = $value;
}

global $arUser, $userId, $isAuthorized;
?>

<div class="reviews">
    <div class="reviews__top">
        <div class="reviews__heading">Отзывы</div>
        <? if ($arReviews): ?>
            <div class="reviews__preview">
                <div class="score">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt>
                    <span><?= $avgRating ?></span>
                </div>
                <span class="map-ellips"></span>
                <span class="reviews__top-count"><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?></span>
            </div>
        <? endif; ?>
    </div>

    <div class="reviews__invite">
        <? if ($isUserReview == 'Y') { ?>
            <span>Вы уже оставляли отзыв на этот объект</span>
        <? } else { ?>
            <span>Для того, чтобы оставить отзыв, вам необходимо авторизоваться</span>
            <? if (!$isAuthorized) { ?>
                <a class="reviews__add" href="#login-phone" data-modal="">Оставить отзыв</a>
            <? } else { ?>
                <button
                    class="reviews__add"
                    type="button"
                    data-modal-review="0"
                    data-before-review-add
                    data-order-id="0"
                    data-camping-id="<?= $sectionId ?>">
                    Оставить отзыв
                </button>
            <? } ?>
        <? } ?>
    </div>

    <div class="reviews__reviews" data-object-reviews-container>
        <a class="anchor"></a>
        <? if ($arReviews && $arResult['reviewsYandex']): ?>
            <div class="sort">
                <ul class="list">
                    <?/*li class="list__item">
                    <?if ($reviewsSortType == "date") : ?>
                        <span class="list__link" data-sort="date">Свежие</span>
                    <? else : ?>
                        <a class="list__link" href="#" data-sort="date">Свежие</a>
                    <? endif; ?>
                    <div class="list__link active"><?= Loc::getMessage('NATURALIST_REVIEWS'); ?></div>
                </li>
                <li class="list__item">
                    <? if ($reviewsSortType == "positive") : ?>
                        <span class="list__link" data-sort="positive">Положительные</span>
                    <? else : ?>
                        <a class="list__link" href="#" data-sort="positive">Положительные</a>
                    <? endif;  ?>
                    <div class="list__link"><?= Loc::getMessage('YANDEX_REVIEWS'); ?></div>
                </li>
                <?li class="list__item">
                    <? if ($reviewsSortType == "negative") : ?>
                        <span class="list__link" data-sort="negative">Отрицательные</span>
                    <? else : ?>
                        <a class="list__link" href="#" data-sort="negative">Отрицательные</a>
                    <? endif; ?>
                </li*/ ?>
                    <li class="list__item reviews__item">
                        <div class="list__link<?= !empty($arReviews) ? ' active' : '' ?>" data-tab="naturalist_review"><?= Loc::getMessage('NATURALIST_REVIEWS'); ?></div>
                    </li>
                    <li class="list__item reviews__item">
                        <div class="list__link<?= !empty($arReviews) ? '' : ' active' ?>" data-tab="yandex_review"><?= Loc::getMessage('YANDEX_REVIEWS'); ?></div>
                    </li>
                </ul>
            </div>
        <? endif; ?>
        <div class="reviews__list naturalist_review<?= !empty($arReviews) ? ' active' : '' ?>">
            <? if ($arReviews): ?>
                <? foreach ($arReviews as $arItem) : ?>
                    <?
                    $isAuthor = ($arReviewsLikesData["USERS"][$arItem["ID"]] == $userId);
                    $value = $arReviewsLikesData["ITEMS"][$arItem["ID"]][$userId];

                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                    <div class="review" data-id="<?= $arItem["ID"] ?>" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                        <div class="review__content">
                            <div class="review__heading">
                                <div class="review__image">
                                    <? if ($arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["PERSONAL_PHOTO"]) : ?>
                                        <img width="54" height="54" src="<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["PERSONAL_PHOTO"] ?>" alt="<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?>">
                                    <? else : ?>
                                        <img width="54" height="54" src="<?= SITE_TEMPLATE_PATH . "/img/default_avatar.svg" ?>" alt="<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?>">
                                    <? endif; ?>
                                </div>
                                <div class="review__title">
                                    <div class="review__name">
                                        <?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?>
                                        <div class="review__rating">
                                            <img src="/local/templates/main/assets/img/star-score.svg" alt="">
                                            <?= number_format(floatval($arItem["PROPERTY_RATING_VALUE"]), 1, '.') ?>
                                        </div>
                                    </div>
                                    <span><?= FormatDate("d F Y г.", strtotime($arItem["ACTIVE_FROM"])) ?></span>
                                </div>
                                <div class="review__likes">
                                    <button class="review__likes-like <? if ($isAuthor && $value == 1) : ?>review__likes_active<? endif; ?>" data-id="<?= $arItem["ID"] ?>" data-value="1" <? if (!$isAuthor) : ?>data-like-add<? endif; ?><? if ($isAuthor && $value == 1) : ?>data-like-delete<? endif; ?>>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M7.44975 15.6848C7.39492 19.2526 10.0995 21.9505 14.3482 21.9896L15.5817 21.9993C16.7695 22.0091 17.6467 21.9114 18.1401 21.7647C18.8528 21.579 19.5381 21.1196 19.5381 20.191C19.5381 19.8195 19.4467 19.5361 19.3371 19.3308C19.264 19.2135 19.2731 19.106 19.3919 19.0571C19.9492 18.8127 20.4244 18.236 20.4244 17.4736C20.4244 17.0239 20.3056 16.6427 20.0954 16.369C19.9949 16.2224 20.0132 16.0953 20.1777 15.9878C20.5888 15.7337 20.8812 15.1863 20.8812 14.5509C20.8812 14.0915 20.7442 13.6125 20.5066 13.3779C20.3513 13.2411 20.3787 13.1433 20.534 12.9967C20.8173 12.7426 21 12.3027 21 11.7455C21 10.8071 20.3147 10.0349 19.4102 10.0349H16.1939C15.3807 10.0349 14.8416 9.58528 14.8416 8.86194C14.8416 7.56189 16.3584 5.16705 16.3584 3.44668C16.3584 2.52784 15.8193 2 15.0975 2C14.4487 2 14.1289 2.47897 13.7817 3.21208C12.4203 6.03701 10.6112 8.32432 9.23147 10.2891C8.06193 11.9508 7.48629 13.3779 7.44975 15.6848ZM3 15.7532C3 18.6466 4.69949 21.0707 6.95635 21.0707H8.56447C6.9198 19.7902 6.18883 17.8548 6.22538 15.6555C6.25279 13.2117 7.13909 11.462 7.93401 10.4064H6.61827C4.58071 10.4064 3 12.7523 3 15.7532Z" fill="#E39250" />
                                        </svg>
                                        <span><?= (int)$arItem["LIKES"][1] ?></span>
                                    </button>

                                    <button class="review__likes-dislike <? if ($isAuthor && isset($value) && $value == 0) : ?>review__likes_active<? endif; ?>" data-id="<?= $arItem["ID"] ?>" data-value="0" <? if (!$isAuthor) : ?>data-like-add<? endif; ?><? if ($isAuthor && isset($value) && $value == 0) : ?>data-like-delete<? endif; ?>>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.4914 8.7101C16.4914 8.70564 16.4915 8.70119 16.4916 8.69674C16.4916 8.69688 16.4916 8.69661 16.4916 8.69674C16.5077 7.45351 16.288 6.304 15.8041 5.33636C15.5199 4.76781 15.1444 4.2619 14.6721 3.83636H15.9228C16.2218 3.83636 16.5082 3.89965 16.7776 4.01754C18.0901 4.5918 19 6.46162 19 8.62212C19 10.8485 18.1646 12.6743 16.9833 13.2471C16.7316 13.3691 16.4642 13.4343 16.1858 13.4343H15.1624C15.4182 13.0412 15.6862 12.5412 15.914 11.9343C16.2368 11.0742 16.4789 9.99951 16.4914 8.7101ZM15.5391 8.6837C15.539 8.68794 15.539 8.69218 15.5389 8.69642C15.5389 8.6979 15.5389 8.69938 15.5388 8.70086C15.512 10.5075 15.1663 11.7142 14.4734 12.9859C14.3739 13.1686 14.2672 13.3525 14.1533 13.5398C13.9488 13.8769 13.7321 14.2245 13.508 14.584C13.436 14.6995 13.3632 14.8163 13.2898 14.9343C12.3935 16.3758 11.4064 18.007 10.6142 19.9091C10.3442 20.5689 10.0954 21 9.59086 21C9.02944 21 8.61015 20.5249 8.61015 19.698C8.61015 18.8697 8.94776 17.8677 9.26176 16.9357C9.53472 16.1256 9.78985 15.3684 9.78985 14.8243C9.78985 14.1733 9.37056 13.7686 8.73807 13.7686H6.23655C5.53299 13.7686 5 13.0736 5 12.229C5 11.7276 5.14213 11.3317 5.36244 11.103C5.48325 10.971 5.50457 10.883 5.38376 10.7599C5.19898 10.5487 5.09239 10.1177 5.09239 9.7042C5.09239 9.13237 5.3198 8.63972 5.63959 8.41098C5.76751 8.31421 5.78173 8.19985 5.70355 8.06789C5.5401 7.82156 5.44772 7.47847 5.44772 7.07379C5.44772 6.38759 5.81726 5.86855 6.25076 5.64861C6.34315 5.60463 6.35025 5.50786 6.2934 5.40229C6.20812 5.21754 6.13706 4.96242 6.13706 4.62812C6.13706 3.79237 6.67005 3.3789 7.22437 3.21175C7.60812 3.07979 8.29036 2.99181 9.21421 3.00061L10.1736 3.00941C10.7379 3.01542 11.2671 3.09119 11.7566 3.22872C12.6546 3.481 13.419 3.9411 14.0206 4.55953C15.0116 5.57831 15.5611 7.02676 15.5391 8.6837ZM13.5343 2.33636H15.9228C17.4856 2.33636 18.6686 3.31245 19.388 4.45195C20.1131 5.60041 20.5 7.08501 20.5 8.62212C20.5 10.1807 20.1483 11.6574 19.4824 12.7975C18.8295 13.9155 17.7148 14.9343 16.1858 14.9343H15.0571C14.958 15.0943 14.8578 15.255 14.757 15.4169C13.811 16.9358 12.8057 18.5498 12.0006 20.4817C11.8656 20.8113 11.6632 21.2789 11.3414 21.664C10.9538 22.1277 10.3686 22.5 9.59086 22.5C8.84941 22.5 8.17042 22.1696 7.7117 21.5874C7.27533 21.0335 7.11015 20.3513 7.11015 19.698C7.11015 18.6339 7.50077 17.4678 7.77797 16.642C7.79972 16.5771 7.82102 16.5139 7.84182 16.4521C7.96902 16.0744 8.07796 15.7509 8.16221 15.4532C8.18126 15.3859 8.19766 15.3244 8.21171 15.2686H6.23655C4.39718 15.2686 3.5 13.5569 3.5 12.229C3.5 11.7772 3.57606 11.2947 3.76548 10.8514C3.74803 10.7969 3.73292 10.7448 3.71979 10.6958C3.63364 10.3739 3.59239 10.0317 3.59239 9.7042C3.59239 9.12943 3.73494 8.50362 4.0537 7.9726C3.97965 7.67424 3.94772 7.36948 3.94772 7.07379C3.94772 6.2907 4.21842 5.58269 4.66052 5.04635C4.64526 4.91282 4.63706 4.77337 4.63706 4.62812C4.63706 3.86312 4.89193 3.19192 5.34983 2.6748C5.7765 2.19295 6.3058 1.92614 6.76432 1.78387C7.38883 1.57541 8.26243 1.49151 9.22797 1.50067L10.1896 1.50949C11.4253 1.52265 12.5584 1.81143 13.5343 2.33636Z" fill="#0B0C0E" />
                                        </svg>
                                        <span><?= (int)$arItem["LIKES"][0] ?></span>
                                    </button>
                                </div>
                            </div>

                            <div class="review__text">
                                <div class="review__text-container">
                                    <div>
                                        <p><?= $arItem["DETAIL_TEXT"] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <? if ($arItem["PICTURES"]) : ?>
                            <div class="review__gallery">
                                <ul class="list list_gallery">
                                    <? foreach ($arItem["PICTURES_THUMB"] as $key => $srcThumb) : ?>
                                        <li class="list__item">
                                            <a class="list__link" href="<?= $arItem["PICTURES"][$key] ?>" data-fancybox="reviewGallery" data-caption='<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?>'>
                                                <img src="<?= $srcThumb["src"] ?>" alt>
                                            </a>
                                        </li>
                                    <? endforeach; ?>
                                </ul>
                            </div>
                        <? endif; ?>
                    </div>
                <? endforeach; ?>
            <? else: ?>
                пока нет отзывов
            <? endif; ?>
        </div>

        <? if ($reviewsPage < $reviewsPageCount) : ?>
            <a class="reviews__more" href="#" data-object-reviews-showmore data-page="<?= $reviewsPage + 1 ?>">Показать ещё</a>
        <? endif; ?>
    </div>
    <div class="reviews__list yandex_review<?= !empty($arReviews) ? '' : ' active' ?>">
        <script src="https://res.smartwidgets.ru/app.js" ; defer></script>
        <div class="sw-app" data-app="<?= $arResult['reviewsYandex'][0]['UF_ID_YANDEX'] ?>"></div>
    </div>
</div>