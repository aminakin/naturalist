<?/*<div class="modal modal_gallery" id="gallery">
    <button class="modal__close" data-modal-close>
        <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
            <use xlink:href="#cross" />
        </svg>
    </button>
    <div class="modal__container" data-modal-gallery></div>
</div>*/ ?>

<div class="modal modal_map" id="modal-map">
    <button class="modal__close" data-modal-close>
        <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
            <use xlink:href="#cross" />
        </svg>
    </button>
    <div class="modal__container">
        <div id="map-large"></div>
    </div>
</div>

<div class="modal modal_room-more" id="more">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div data-room-more-content></div>
    </div>
</div>

<div class="modal modal_detail-more" id="detail-more">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div data-detail-more-content></div>
    </div>
</div>

<div class="modal modal_comfort-more" id="feature-more">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__title">Развлечения</div>
        <div data-feature-more-content>
            <ul class="object__comforts-list">
                <? foreach ($arParams['OBJECT_FUN'] as $feat) { ?>
                    <li>
                        <? if ($feat['ELEMENT']) { ?>
                            <a href="#" class="getDetail" elementId="<?= $feat['ELEMENT'] ?>"><?= $feat['UF_NAME'] ?></a>
                        <? } else {
                            echo $feat['UF_NAME'];
                        } ?>
                    </li>
                <? } ?>
            </ul>
        </div>
    </div>
</div>

<div class="modal modal_comfort-more" id="comfort-more">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__title">Удобства</div>
        <div data-comfort-more-content>
            <ul class="object__comforts-list">
                <? foreach ($arParams['OBJECT_COMFORTS'] as $comfort) { ?>
                    <li>
                        <? if ($comfort['ELEMENT']) { ?>
                            <a class="getDetail" href="#" elementId="<?= $comfort['ELEMENT'] ?>"><?= $comfort['UF_NAME'] ?></a>
                        <? } else {
                            echo $comfort['UF_NAME'];
                        } ?>
                    </li>
                <? } ?>
            </ul>
        </div>
    </div>
</div>

<div class="modal modal_semi-galery" id="gallery">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__title"><?= $arParams['TITLE'] ?></div>
        <div class="modal__content">
            <? if ($arParams['SECTION_IMGS']) { ?>
                <? foreach ($arParams['SECTION_IMGS'] as $key => $img) { ?>
                    <a class="modal__img" href="<?= $img['big'] ?>" data-fancybox="gallery" data-caption='<?= $arParams['TITLE'] ?>'>
                        <img loading="lazy" src="<?= $img['src'] ?>" alt="">
                    </a>
                <? } ?>
            <? } ?>
        </div>
    </div>
</div>

<div class="modal modal_detail-more houses" id="houses">
    <div class="modal__container">
        <button class="modal__close" data-modal-close>
            <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                <use xlink:href="#cross" />
            </svg>
        </button>
        <div class="modal__title">Типы домов</div>
        <div data-houses-more-content>
            <div class="modal__house-list" style="grid-template-columns:repeat(<?= round(sqrt(count($arParams['SECTION']['UF_SUIT_TYPE'])), 0, PHP_ROUND_HALF_UP) + 1 ?>, 1fr)">
                <? foreach ($arParams['SECTION']['UF_SUIT_TYPE'] as $key => $suitType) { ?>
                    <div class="modal__item-house">
                        <img width="40" src="<?= CFile::getPath($arParams['HOUSE_TYPES'][$suitType]['UF_IMG']) ?>" alt="">
                        <span><?= $arParams['HOUSE_TYPES'][$suitType]['UF_NAME'] ?></span>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</div>

<?
$APPLICATION->IncludeComponent(
    "naturalist:reviews.add",
    "",
    [],
    false
);
?>