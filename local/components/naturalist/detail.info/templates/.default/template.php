<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="detail-feature">
    <div class="detail-feature__title"><?= $arResult['NAME'] ?></div>
    <? if ($arResult['IMG']) { ?>
        <div class="swiper detail-feature__slider">
            <div class="swiper-wrapper">
                <? foreach ($arResult['IMG'] as $img) { ?>
                    <div class="swiper-slide">
                        <img src="<?= CFile::ResizeImageGet($img, array('width' => 460, 'height' => 330), BX_RESIZE_IMAGE_EXACT, true)['src'] ?>" alt="">
                    </div>
                <? } ?>
            </div>
            <div class="swiper-button-prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                    <g filter="url(#filter0_b_6845_59178)">
                        <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5752 14.0495C17.7949 14.2692 17.7949 14.6253 17.5752 14.845L13.4205 18.9998L17.5752 23.1545C17.7949 23.3742 17.7949 23.7303 17.5752 23.95C17.3556 24.1697 16.9994 24.1697 16.7798 23.95L12.2273 19.3975C12.1218 19.292 12.0625 19.149 12.0625 18.9998C12.0625 18.8506 12.1218 18.7075 12.2273 18.602L16.7798 14.0495C16.9994 13.8298 17.3556 13.8298 17.5752 14.0495Z" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.1899 19C12.1899 18.6893 12.4418 18.4375 12.7524 18.4375H25.3749C25.6856 18.4375 25.9374 18.6893 25.9374 19C25.9374 19.3107 25.6856 19.5625 25.3749 19.5625H12.7524C12.4418 19.5625 12.1899 19.3107 12.1899 19Z" fill="white" />
                    </g>
                    <defs>
                        <filter id="filter0_b_6845_59178" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                            <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_6845_59178" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_6845_59178" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                    <g filter="url(#filter0_b_6845_59184)">
                        <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4245 14.0495C20.6442 13.8298 21.0003 13.8298 21.22 14.0495L25.7725 18.602C25.878 18.7075 25.9373 18.8506 25.9373 18.9998C25.9373 19.149 25.878 19.292 25.7725 19.3975L21.22 23.95C21.0003 24.1697 20.6442 24.1697 20.4245 23.95C20.2048 23.7303 20.2048 23.3742 20.4245 23.1545L24.5793 18.9998L20.4245 14.845C20.2048 14.6253 20.2048 14.2692 20.4245 14.0495Z" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0625 19C12.0625 18.6893 12.3143 18.4375 12.625 18.4375H25.2475C25.5582 18.4375 25.81 18.6893 25.81 19C25.81 19.3107 25.5582 19.5625 25.2475 19.5625H12.625C12.3143 19.5625 12.0625 19.3107 12.0625 19Z" fill="white" />
                    </g>
                    <defs>
                        <filter id="filter0_b_6845_59184" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                            <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_6845_59184" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_6845_59184" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </div>
        </div>
    <? } ?>
    <div class="detail-feature__text">
        <div class="detail-feature__text-title">Описание</div>
        <div class="detail-feature__text-text">
            <?= $arResult['TEXT'] ?>
        </div>
    </div>
    <div class="detail-feature__bottom">
        <span>Услуга оплачивается отдельно</span>
        <? if ($arResult['DATE']) { ?>
            <span><?= $arResult['DATE'] ?></span>
        <? } ?>
    </div>
</div>