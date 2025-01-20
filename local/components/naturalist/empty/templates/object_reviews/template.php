<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

foreach ($arResult as $key => $value) {
    ${$key} = $value;
}

global $arUser, $userId, $isAuthorized;
?>
<div class="reviews">
    <div class="reviews__heading h3">Отзывы</div>
    <div class="reviews__preview">
        <div class="score">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/score.svg" alt>
            <span><?= $avgRating ?></span>
        </div>
        <span><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?></span>
    </div>



    <div class="reviews__reviews" data-object-reviews-container>
        <a class="anchor"></a>
        <div class="sort">
            <span>Показывать сначала:</span>
            <ul class="list">
                <li class="list__item">
                    <? if ($reviewsSortType == "date") : ?>
                        <span class="list__link" data-sort="date">Свежие</span>
                    <? else : ?>
                        <a class="list__link" href="#" data-sort="date">Свежие</a>
                    <? endif; ?>
                </li>
                <li class="list__item">
                    <? if ($reviewsSortType == "positive") : ?>
                        <span class="list__link" data-sort="positive">Положительные</span>
                    <? else : ?>
                        <a class="list__link" href="#" data-sort="positive">Положительные</a>
                    <? endif; ?>
                </li>
                <li class="list__item">
                    <? if ($reviewsSortType == "negative") : ?>
                        <span class="list__link" data-sort="negative">Отрицательные</span>
                    <? else : ?>
                        <a class="list__link" href="#" data-sort="negative">Отрицательные</a>
                    <? endif; ?>
                </li>
            </ul>
        </div>

        <div class="reviews__list">
            <? foreach ($arReviews as $arItem) : ?>
                <?
                $isAuthor = ($arReviewsLikesData["USERS"][$arItem["ID"]] == $userId);
                $value = $arReviewsLikesData["ITEMS"][$arItem["ID"]][$userId];

                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="review" data-id="<?= $arItem["ID"] ?>" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                    <div class="review__image">
                        <? if ($arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["PERSONAL_PHOTO"]) : ?>
                            <img src="<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["PERSONAL_PHOTO"] ?>" alt="<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?>">
                        <? else : ?>
                            <img src="<?= SITE_TEMPLATE_PATH . "/img/default_avatar.svg" ?>" alt="<?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?>">
                        <? endif; ?>
                    </div>

                    <div class="review__content">
                        <div class="review__heading">
                            <div class="review__title">
                                <div class="h3"><?= $arReviewsUsers[$arItem["PROPERTY_USER_ID_VALUE"]]["NAME"] ?></div>
                                <span><?= FormatDate("d F Y г.", strtotime($arItem["ACTIVE_FROM"])) ?></span>
                            </div>
                            <ul class="list list_stars">
                                <? for ($i = 1; $i <= 5; $i++) : ?>
                                    <li class="list__item<? if ($i <= floor($arItem["PROPERTY_RATING_VALUE"])) : ?> list__item_active<? endif; ?>">
                                        <svg class="icon icon_star" viewbox="0 0 12 12" style="width: 1.2rem; height: 1.2rem;">
                                            <use xlink:href="#star" />
                                        </svg>
                                    </li>
                                <? endfor; ?>
                            </ul>
                        </div>

                        <div class="review__text">
                            <div class="review__text-container">
                                <div>
                                    <p><?= $arItem["DETAIL_TEXT"] ?></p>
                                </div>
                            </div>
                        </div>

                        <? if ($arItem["PICTURES"]) : ?>
                            <?
                            $galleryStr = htmlspecialchars(json_encode($arItem["PICTURES"]));
                            ?>
                            <div class="review__gallery">
                                <ul class="list list_gallery" data-review-gallery="<?= $galleryStr ?>">
                                    <? foreach ($arItem["PICTURES_THUMB"] as $srcThumb) : ?>
                                        <li class="list__item">
                                            <a class="list__link" href="#" data-review-gallery-item="data-review-gallery-item">
                                                <img src="<?= $srcThumb["src"] ?>" alt>
                                            </a>
                                        </li>
                                    <? endforeach; ?>
                                </ul>
                            </div>
                        <? endif; ?>
                    </div>

                    <div class="review__likes">
                        <button class="review__likes-like <? if ($isAuthor && $value == 1) : ?>review__likes_active<? endif; ?>" data-id="<?= $arItem["ID"] ?>" data-value="1" <? if (!$isAuthor) : ?>data-like-add<? endif; ?><? if ($isAuthor && $value == 1) : ?>data-like-delete<? endif; ?>>
                            <svg class="icon icon_like" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                <use xlink:href="#like" />
                            </svg>
                            <span><?= (int)$arItem["LIKES"][1] ?></span>
                        </button>

                        <button class="review__likes-dislike <? if ($isAuthor && isset($value) && $value == 0) : ?>review__likes_active<? endif; ?>" data-id="<?= $arItem["ID"] ?>" data-value="0" <? if (!$isAuthor) : ?>data-like-add<? endif; ?><? if ($isAuthor && isset($value) && $value == 0) : ?>data-like-delete<? endif; ?>>
                            <svg class="icon icon_like" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                <use xlink:href="#like" />
                            </svg>
                            <span><?= (int)$arItem["LIKES"][0] ?></span>
                        </button>
                    </div>
                </div>
            <? endforeach; ?>
        </div>

        <? if ($reviewsPage < $reviewsPageCount) : ?>
            <div class="reviews__more">
                <a href="#" data-object-reviews-showmore data-page="<?= $reviewsPage + 1 ?>">Показать ещё</a>
            </div>
        <? endif; ?>
    </div>
</div>