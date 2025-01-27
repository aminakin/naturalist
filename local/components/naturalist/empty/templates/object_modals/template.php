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

<?
$APPLICATION->IncludeComponent(
    "naturalist:reviews.add",
    "",
    [],
    false
);
?>