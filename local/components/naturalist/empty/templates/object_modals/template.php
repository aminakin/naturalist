<?
foreach($arResult as $key => $value) {
    ${$key} = $value;
}
?>
<div class="modal modal_gallery" id="gallery">
    <button class="modal__close" data-modal-close>
        <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
            <use xlink:href="#cross" />
        </svg>
    </button>
    <div class="modal__container" data-modal-gallery></div>
</div>

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
            <use xlink:href="#cross"/>
            </svg>
        </button>
        <div data-room-more-content></div>
    </div>
</div>