<?
foreach ($arResult as $key => $value) {
    ${$key} = $value;
}

global $isMobile;

?>
<section class="section section_object">
    <div class="container">
        <div class="object-hero">
            <div class="object__top">
                <div class="object-hero__heading">
                    <h1><?= htmlspecialcharsBack($arParams["h1SEO"]); ?></h1>
                    <? if ($reviewsCount > 0): ?>
                        <div class="object-hero__reviews">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                            <span><?= $avgRating ?></span>
                        </div>
                    <? endif; ?>
                </div>
                <div class="area-info">
                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/marker.svg" alt width="20" height="20">
                    <? if (!empty($arSection["UF_ADDRESS"])) : ?>
                        <span><?= htmlspecialcharsBack($arSection["UF_ADDRESS"]) ?></span>
                    <? endif; ?>
                </div>
                <button class="favorite top <?= ($arFavourites && in_array($arSection["ID"], $arFavourites)) ? ' active' : '' ?>"
                        <? if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<? else: ?>data-favourite-add<? endif; ?>
                        data-id="<?= $arSection["ID"] ?>">
                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z"
                              fill="#E39250"></path>
                    </svg>
                </button>
            </div>
            <div class="object-hero__gallery">
                <button class="favorite top <?= ($arFavourites && in_array($arSection["ID"], $arFavourites)) ? ' active' : '' ?> mobile"
                        <? if ($arFavourites && in_array($arSection["ID"], $arFavourites)) : ?>data-favourite-remove<? else: ?>data-favourite-add<? endif; ?>
                        data-id="<?= $arSection["ID"] ?>">
                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M3.77566 2.51612C6.01139 1.14472 8.01707 1.69128 9.22872 2.60121C9.42805 2.75091 9.56485 2.85335 9.6667 2.92254C9.76854 2.85335 9.90535 2.75091 10.1047 2.60121C11.3163 1.69128 13.322 1.14472 15.5577 2.51612C17.1037 3.4644 17.9735 5.44521 17.6683 7.72109C17.3616 10.008 15.8814 12.5944 12.7467 14.9146C12.7205 14.934 12.6945 14.9533 12.6687 14.9724C11.5801 15.7786 10.8592 16.3125 9.6667 16.3125C8.47415 16.3125 7.75326 15.7786 6.66473 14.9724C6.63893 14.9533 6.61292 14.934 6.5867 14.9146C3.452 12.5944 1.97181 10.008 1.6651 7.72109C1.35986 5.44521 2.22973 3.4644 3.77566 2.51612ZM9.54914 2.99503C9.54673 2.99611 9.54716 2.99576 9.55019 2.99454L9.54914 2.99503ZM9.78321 2.99454C9.78624 2.99576 9.78667 2.99611 9.78426 2.99503L9.78321 2.99454Z"
                              fill="#E39250"></path>
                    </svg>
                </button>
                <? if ($isMobile) { ?>
                    <div class="swiper slider-gallery" data-slider-object="data-slider-object"
                         data-fullgallery="[<?= $arSection["FULL_GALLERY"]; ?>]">
                        <div class="swiper-wrapper">
                            <? $keyPhoto = 1; ?>
                            <? foreach ($arSection["PICTURES"] as $arPhoto) : ?>
                                <? if (count($arSection["PICTURES"]) > 1): ?>
                                    <?
                                    $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " " . $arSection["UF_REGION_NAME"] . " рис." . $keyPhoto;;
                                    $title = "Фото - " . $arSection["NAME"] . " рис." . $keyPhoto;
                                    ?>
                                <? else: ?>
                                    <?
                                    $alt = $arHLTypes[$arSection["UF_TYPE"]]["UF_NAME"] . " " . $arSection["NAME"] . " " . $arSection["UF_REGION_NAME"];
                                    $title = "Фото - " . $arSection["NAME"];
                                    ?>

                                <? endif; ?>
                                <div class="swiper-slide">
                                    <a href="#gallery" data-modal>
                                        <img class="swiper-lazy" alt="<?= $alt ?>" title="<?= $title ?>"
                                             data-src="<?= $arPhoto["src"] ?>">
                                    </a>
                                </div>
                                <? $keyPhoto++; ?>
                            <? endforeach; ?>
                        </div>

                        <? if (count($arSection["PICTURES"]) > 1) : ?>
                            <div class="swiper-button-prev">
                                <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                     style="width: 1.6rem; height: 1.6rem;">
                                    <use xlink:href="#arrow-small"/>
                                </svg>
                            </div>
                            <div class="swiper-button-next">
                                <svg class="icon icon_arrow-small" viewbox="0 0 16 16"
                                     style="width: 1.6rem; height: 1.6rem;">
                                    <use xlink:href="#arrow-small"/>
                                </svg>
                            </div>
                            <div class="swiper-pagination"></div>
                        <? endif; ?>
                    </div>
                <? } else { ?>
                    <div class="object__galery <?= count($arSection["PICTURES"]) == 1 ? 'alone' : '' ?>">
                        <a href="#gallery" data-modal class="object__galery-item first">
                            <img src="<?= $arSection["PICTURES"][0]['big'] ? $arSection["PICTURES"][0]['big'] : $arSection["PICTURES"][0]['src'] ?>"
                                 loading="lazy" alt="">
                        </a>
                        <? if (count($arSection["PICTURES"]) > 1) { ?>
                            <a href="#gallery" data-modal class="object__galery-item">
                                <img src="<?= $arSection["PICTURES"][1]['src'] ?>" loading="lazy" alt="">
                            </a>
                            <a href="#gallery" data-modal class="object__galery-item">
                                <img src="<?= $arSection["PICTURES"][2]['src'] ?>" loading="lazy" alt="">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20"
                                         fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M10.452 1.4585H10.5473C12.3729 1.45848 13.8068 1.45848 14.9263 1.60899C16.0735 1.76323 16.9837 2.08576 17.6989 2.80092C18.4141 3.51609 18.7366 4.4263 18.8908 5.57352C19.0414 6.69303 19.0414 8.12693 19.0413 9.95252V10.0478C19.0414 11.8734 19.0414 13.3073 18.8908 14.4268C18.7366 15.574 18.4141 16.4842 17.6989 17.1994C16.9837 17.9146 16.0735 18.2371 14.9263 18.3913C13.8068 18.5419 12.3729 18.5418 10.5473 18.5418H10.452C8.62644 18.5418 7.19254 18.5419 6.07303 18.3913C4.92581 18.2371 4.0156 17.9146 3.30044 17.1994C2.58527 16.4842 2.26274 15.574 2.1085 14.4268C1.95799 13.3073 1.958 11.8734 1.95801 10.0478V9.95252C1.958 8.12693 1.95799 6.69303 2.1085 5.57352C2.26274 4.4263 2.58527 3.51609 3.30044 2.80092C4.0156 2.08576 4.92581 1.76323 6.07303 1.60899C7.19254 1.45848 8.62644 1.45848 10.452 1.4585ZM6.23959 2.84784C5.23098 2.98345 4.62852 3.2406 4.18432 3.68481C3.74012 4.12901 3.48296 4.73147 3.34736 5.74008C3.20934 6.76666 3.20801 8.11652 3.20801 10.0002C3.20801 11.8838 3.20934 13.2337 3.34736 14.2603C3.48296 15.2689 3.74012 15.8713 4.18432 16.3155C4.62852 16.7597 5.23098 17.0169 6.23959 17.1525C7.26617 17.2905 8.61603 17.2918 10.4997 17.2918C12.3833 17.2918 13.7332 17.2905 14.7598 17.1525C15.7684 17.0169 16.3708 16.7597 16.815 16.3155C17.2592 15.8713 17.5164 15.2689 17.652 14.2603C17.79 13.2337 17.7913 11.8838 17.7913 10.0002C17.7913 8.11652 17.79 6.76666 17.652 5.74008C17.5164 4.73147 17.2592 4.12901 16.815 3.68481C16.3708 3.2406 15.7684 2.98345 14.7598 2.84784C13.7332 2.70982 12.3833 2.7085 10.4997 2.7085C8.61603 2.7085 7.26617 2.70982 6.23959 2.84784Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M14.25 5.625C13.9048 5.625 13.625 5.90482 13.625 6.25C13.625 6.59518 13.9048 6.875 14.25 6.875C14.5952 6.875 14.875 6.59518 14.875 6.25C14.875 5.90482 14.5952 5.625 14.25 5.625ZM12.375 6.25C12.375 5.21447 13.2145 4.375 14.25 4.375C15.2855 4.375 16.125 5.21447 16.125 6.25C16.125 7.28553 15.2855 8.125 14.25 8.125C13.2145 8.125 12.375 7.28553 12.375 6.25Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M10.0368 14.1233C8.30234 12.903 6.11248 12.2499 3.85796 12.2941L3.84425 12.2942C3.57591 12.2936 3.30781 12.3022 3.04057 12.3198L2.95801 11.0726C3.25142 11.0532 3.54574 11.0437 3.8403 11.0442C6.34329 10.9965 8.79405 11.7205 10.7561 13.101C12.5786 14.3833 13.8764 16.1608 14.4347 18.166L13.2305 18.5013C12.7563 16.7979 11.6441 15.2542 10.0368 14.1233Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M15.822 12.7085C14.1169 12.7022 12.4571 13.3308 10.9482 14.5073L11.7168 15.4931C13.0433 14.4588 14.4379 13.9531 15.8186 13.9585L15.8217 13.9585C16.6061 13.9577 17.3926 14.1237 18.168 14.4587L18.6637 13.3111C17.7417 12.9129 16.7871 12.7078 15.822 12.7085Z"
                                              fill="white"/>
                                    </svg>
                                    <?= count($arSection["PICTURES"]) ?> фото
                                </span>
                            </a>
                        <? } ?>
                    </div>
                <? } ?>
            </div>

            <div class="object__detail-info">
                <div class="object__info-left">
                    <? if (!empty($arSection["DESCRIPTION"])) { ?>
                        <div class="object__description">
                            <div class="about__text about__text_hidden">
                                <div class="about__text-content">
                                    <div class="h3">Об этом месте</div>
                                    <div class="about__text-hide" data-text-show>
                                        <div><?= htmlspecialcharsBack($arSection["~DESCRIPTION"]) ?></div>
                                    </div>
                                </div>

                                <div class="about__text-show">
                                    <div class="about__text-hide additional-text" style="text-decoration: underline">...</div>
                                    <a href="#" data-text-show-control data-text-show-more="Развернуть"
                                       data-text-show-hide="Скрыть"></a>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    <div class="about">
                        <div class="about__item">
                            <div class="about__item-title">Типы домов</div>
                            <div class="about__item-content">
                                <div class="about__item-list">
                                    <? foreach ($arSection['UF_SUIT_TYPE'] as $key => $suitType) { ?>
                                        <? if ($key > 3) {
                                            break;
                                        } ?>
                                        <div class="about__item-house">
                                            <img width="32"
                                                 src="<?= CFile::getPath($houseTypeData[$suitType]['UF_IMG']) ?>"
                                                 alt="">
                                            <span><?= $houseTypeData[$suitType]['UF_NAME'] ?></span>
                                        </div>
                                    <? } ?>
                                    <? if (count($arSection['UF_SUIT_TYPE']) > 4) { ?>
                                        <a href="#houses" data-modal
                                           class="object__house-more">Ещё <?= intval(count($arSection['UF_SUIT_TYPE']) - 4) ?></a>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                        <? if ($arServices): ?>
                            <div class="about__item">
                                <div class="about__item-title">Окружение</div>
                                <div class="about__item-content about__item-aroun">
                                    <? foreach ($arServices as $key => $arServiceItem) { ?>
                                        <span><?= $key !== 0 ? ', ' . $arServiceItem["NAME"] : $arServiceItem["NAME"] ?></span>
                                    <? } ?>
                                </div>
                            </div>
                        <? endif; ?>
                        <div class="about__item">
                            <div class="about__item-title">Важная информация</div>
                            <div class="about__item-content about__item-important">
                                <? if (in_array(4, $arSection['UF_REST_VARIANTS'])) { ?>
                                    <div class="about__item-domestic">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 20 20" fill="none">
                                            <path d="M16.0783 6.73408C16.489 6.6994 16.9388 6.81365 17.26 7.0783C17.6796 7.42405 17.8902 7.93488 17.9368 8.46869C17.999 9.1818 17.7269 9.92307 17.272 10.4673C16.8783 10.9383 16.3122 11.3411 15.6852 11.3974L15.6689 11.3997C15.2211 11.4579 14.8056 11.3243 14.4532 11.0474C14.0974 10.768 13.8637 10.2919 13.8114 9.84627C13.719 9.05996 13.9614 8.31129 14.4504 7.69356C14.852 7.18631 15.4274 6.8102 16.0783 6.73408Z"
                                                  fill="black"/>
                                            <path d="M3.76666 6.73486C4.33871 6.75461 4.85266 6.98449 5.26748 7.37863C5.81668 7.90045 6.16543 8.67266 6.18123 9.43256C6.19229 9.96441 6.06635 10.5317 5.68445 10.9259C5.44852 11.1694 5.14127 11.3371 4.8061 11.3942C4.69219 11.4135 4.57651 11.4145 4.46127 11.4163C4.34637 11.41 4.23416 11.402 4.12129 11.3781C3.71096 11.2914 3.33276 11.0712 3.02106 10.7959C2.45014 10.2916 2.08543 9.53889 2.04287 8.77859C2.0124 8.23451 2.1636 7.66867 2.53529 7.25875C2.86014 6.90049 3.29604 6.7575 3.76666 6.73486Z"
                                                  fill="black"/>
                                            <path d="M7.23097 2.81811C7.69628 2.78835 8.15477 3.00952 8.49698 3.30966C9.097 3.83593 9.45569 4.65901 9.50024 5.45087C9.53844 6.13017 9.33649 6.81059 8.87407 7.31767C8.56679 7.6546 8.16737 7.85595 7.71057 7.87843C7.23003 7.90276 6.78227 7.69733 6.4228 7.38977C5.82263 6.87624 5.50028 6.06485 5.44325 5.28974C5.39556 4.64151 5.56805 3.95249 5.9963 3.45343C6.31889 3.07751 6.73768 2.85587 7.23097 2.81811Z"
                                                  fill="black"/>
                                            <path d="M12.5465 2.81814C12.9555 2.78245 13.3691 2.90378 13.6952 3.15409C14.1953 3.53808 14.4702 4.1198 14.5514 4.73704C14.6577 5.5455 14.4248 6.3739 13.9288 7.01804C13.5929 7.45429 13.1237 7.7778 12.5713 7.85151C12.1253 7.89903 11.7053 7.78026 11.3483 7.50831C10.8674 7.14194 10.6086 6.56173 10.5317 5.97349C10.4265 5.16825 10.6743 4.29138 11.1702 3.65054C11.5186 3.20034 11.9762 2.89085 12.5465 2.81814Z"
                                                  fill="black"/>
                                            <path d="M9.73585 8.39486C10.3852 8.32212 11.1575 8.54968 11.6915 8.91794C12.1896 9.26142 12.5765 9.72031 12.842 10.2635C12.9355 10.4549 13.0162 10.6528 13.1142 10.842C13.214 11.0349 13.3387 11.2259 13.4718 11.3974C14.1384 12.2563 15.3137 12.7368 15.5235 13.9019C15.6106 14.3856 15.503 14.8623 15.3256 15.3121C15.2181 15.5845 15.0829 15.8521 14.9137 16.0916C14.5688 16.5796 14.0571 17.0413 13.4527 17.1544C13.2892 17.1849 13.1081 17.1683 12.9449 17.141C12.5753 17.0791 12.206 16.9374 11.8702 16.7739C11.3614 16.5261 10.8798 16.2298 10.3108 16.1436C9.93774 16.0991 9.58503 16.1411 9.22905 16.2608C9.01642 16.3323 8.81462 16.435 8.61397 16.5345C8.08188 16.7982 7.56222 17.0733 6.96343 17.1429L6.91515 17.1483C6.42425 17.1895 5.98438 16.9862 5.61472 16.6764C4.98138 16.1455 4.54569 15.3331 4.47567 14.5082C4.44884 14.1922 4.48159 13.8702 4.59114 13.5715C4.73005 13.1926 4.98956 12.8649 5.27011 12.5801C5.46823 12.379 5.68644 12.1951 5.89503 12.0048C6.1353 11.7855 6.37394 11.5625 6.56495 11.2974C6.823 10.9395 6.99419 10.5434 7.20456 10.1583C7.34524 9.90085 7.50761 9.65652 7.69835 9.4333C8.07276 8.99507 8.6278 8.64117 9.18235 8.4872C9.36466 8.4366 9.54772 8.41048 9.73585 8.39486Z"
                                                  fill="black"/>
                                        </svg>
                                        Можно с животными,
                                    </div>
                                <? } ?>
                                <? if ($arSection["UF_PREVIEW_TEXT"]) { ?>
                                    <div class="about__item-preview-text">
                                        <?= $arSection["UF_PREVIEW_TEXT"] ?>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </div>

                    <? if ($reviewsCount > 0) { ?>
                        <div class="object-hero__reviews-right mobile">
                            <div class="object-hero__reviews-title">Отзывы</div>
                            <div class="object-hero__reviews-info">
                                <a href="#reviews-anchor" data-scroll-to class="score">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <?= $avgRating ?>
                                </a>
                                <span class="map-ellips"></span>
                                <a href="#reviews-anchor"
                                   data-scroll-to=""><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?>
                                </a>
                            </div>
                        </div>
                    <? } ?>

                    <div class="object__links-wrap">
                        <div class="object__links">
                            <? if ($arSection['~UF_LIVING_RULES']) { ?>
                                <a href="#rules-anchor" class="object__page-link" data-scroll-to>Правила проживания</a>
                            <? } ?>
                            <? if ($arObjectComforts) { ?>
                                <a href="#comfort-anchor" class="object__page-link" data-scroll-to>Удобства</a>
                            <? } ?>
                            <? if ($arHLFeatures) { ?>
                                <a href="#fun-anchor" class="object__page-link" data-scroll-to>Развлечения</a>
                            <? } ?>
                        </div>
                    </div>
                    <div class="fake-filter_catalog" id="fake-filter_catalog">
                        <div class="fake-filter_inputs">
                            <div class="fake-filter_date">
                                <span class="from"><?= ($dateFrom) ? $dateFrom : 'Заезд' ?></span>
                                &nbsp;-&nbsp;
                                <span class="to"><?= ($dateTo) ? $dateTo : 'Выезд' ?></span>
                                <span class="fake-filter_guest"><?= $_GET['guests'] ? '<span class="dot"></span>' . $guests + $children . ' ' . $guestsDeclension->get($guests + $children) : '' ?></span>
                            </div>
                        </div>
                        <div class="fake-filter_btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z"
                                      fill="white"/>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z"
                                      fill="white"/>
                            </svg>
                        </div>
                    </div>
                    <div class="object-hero__form catalog_filter catalog_map">
                        <form class="form filters" id="form-object-filter">
                            <div class="form__row calendar" data-calendar="data-calendar" data-calendar-min="today"
                                 data-calendar-max="365">
                                <div class="form__item">
                                    <div class="field field_icon field_calendar start">
                                        <label>Заезд</label>
                                        <div class="field__input" data-calendar-label="data-calendar-label"
                                             data-date-from><? if ($dateFrom): ?><?= $dateFrom ?><? else: ?>
                                                <span></span><? endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form__item">
                                    <div class="field field_icon field_calendar">
                                        <label>Выезд</label>
                                        <div class="field__input" data-calendar-label="data-calendar-label"
                                             data-date-to><? if ($dateTo): ?><?= $dateTo ?><? else: ?>
                                                <span></span><? endif; ?></div>
                                    </div>
                                </div>

                                <div class="calendar__dropdown" data-calendar-dropdown="data-calendar-dropdown">
                                    <div class="calendar__navigation">
                                        <div class="calendar__navigation-item calendar__navigation-item_months">
                                            <div class="calendar__navigation-label"
                                                 data-calendar-navigation="data-calendar-navigation">
                                                <span><?= $currMonthName ?></span>
                                            </div>
                                            <ul class="list">
                                                <?
                                                $k = 0;
                                                ?>
                                                <? foreach ($arDates[0] as $monthName) : ?>
                                                    <li class="list__item<? if ($k == 0) : ?> list__item_active<? endif; ?>">
                                                        <button data-calendar-year="<?= $currYear ?>"
                                                                class="list__item-month"
                                                                data-calendar-month-select="<?= $k ?>"
                                                                type="button"><?= $monthName ?></button>
                                                    </li>
                                                    <? $k++; ?>
                                                <? endforeach ?>
                                                <li class="list__item">
                                                    <div class="list__item-year"><?= $nextYear ?></div>
                                                </li>
                                                <? foreach ($arDates[1] as $monthName) : ?>
                                                    <li class="list__item"
                                                        data-calendar-delimiter="data-calendar-delimiter">
                                                        <button data-calendar-year="<?= $nextYear ?>"
                                                                class="list__item-month"
                                                                data-calendar-month-select="<?= $k ?>"
                                                                type="button"><?= $monthName ?></button>
                                                    </li>
                                                    <? $k++; ?>
                                                <? endforeach ?>
                                            </ul>
                                        </div>

                                        <div class="calendar__navigation-item calendar__navigation-item_years">
                                            <div class="calendar__navigation-label"
                                                 data-calendar-navigation="data-calendar-navigation">
                                                <span><?= $currYear ?></span>
                                            </div>
                                            <ul class="list">
                                                <li class="list__item list__item_active">
                                                    <button data-calendar-year-select="<?= $currYear ?>"
                                                            type="button"><?= $currYear ?></button>
                                                </li>
                                                <li class="list__item">
                                                    <button data-calendar-year-select="<?= $nextYear ?>"
                                                            type="button"><?= $nextYear ?></button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="calendar__month">
                                        <input type="hidden" data-calendar-value="data-calendar-value">
                                    </div>
                                    <div class="calendar__dropdown-close">
                                        Закрыть
                                    </div>
                                </div>
                            </div>

                            <div class="form__row">
                                <div class="form__item">
                                    <div class="field field_icon guests" data-guests="data-guests">
                                        <div class="field__input"
                                             data-guests-control="data-guests-control"><?= $guests + $children ?> <?= $guestsDeclension->get($guests + $children) ?></div>
                                        <div class="guests__dropdown">
                                            <div class="guests__guests">
                                                <div class="guests__item">
                                                    <div class="guests__label">
                                                        <div><?= GetMessage('FILTER_ADULTS') ?></div>
                                                        <span><?= GetMessage('FILTER_ADULTS_AGE') ?></span>
                                                    </div>
                                                    <div class="counter">
                                                        <button class="counter__minus" type="button"></button>
                                                        <input type="text" disabled="disabled"
                                                               data-guests-adults-count="data-guests-adults-count"
                                                               name="guests-adults-count" value="<?= $guests ?>"
                                                               data-min="1"
                                                               data-max="99">
                                                        <button class="counter__plus" type="button"></button>
                                                    </div>
                                                </div>

                                                <div class="guests__item">
                                                    <div class="guests__label">
                                                        <div><?= GetMessage('FILTER_CHILDREN') ?></div>
                                                        <span><?= GetMessage('FILTER_CHILDREN_AGE') ?></span>
                                                    </div>
                                                    <div class="counter">
                                                        <button class="counter__minus" type="button"></button>
                                                        <input type="text" disabled="disabled"
                                                               data-guests-children-count="data-guests-children-count"
                                                               name="guests-children-count" value="<?= $children ?>"
                                                               data-min="0"
                                                               data-max="20">
                                                        <button class="counter__plus" type="button"></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="guests__children" data-guests-children="data-guests-children">
                                                <? if ($arChildrenAge): ?>
                                                    <? foreach ($arChildrenAge as $keyAge => $valueAge): ?>
                                                        <div class="guests__item">
                                                            <div class="guests__label">
                                                                <div><?= GetMessage('FILTER_CHILD_AGE') ?></div>
                                                                <span><?= getChildrenOrderTitle($keyAge + 1) ?> <?= GetMessage('FILTER_CHILD') ?></span>
                                                            </div>
                                                            <div class="counter">
                                                                <button class="counter__minus" type="button">
                                                                    <svg class="icon icon_arrow-small"
                                                                         viewBox="0 0 16 16"
                                                                         style="width: 1.6rem; height: 1.6rem;">
                                                                        <use xlink:href="#arrow-small"></use>
                                                                    </svg>
                                                                </button>
                                                                <input type="text" disabled="" data-guests-children=""
                                                                       name="guests-children-<?= $keyAge ?>"
                                                                       value="<?= $valueAge ?>"
                                                                       data-min="0" data-max="17">
                                                                <button class="counter__plus" type="button">
                                                                    <svg class="icon icon_arrow-small"
                                                                         viewBox="0 0 16 16"
                                                                         style="width: 1.6rem; height: 1.6rem;">
                                                                        <use xlink:href="#arrow-small"></use>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <? endforeach; ?>
                                                <? endif; ?>
                                            </div>
                                            <div class="guests__dropdown-close">
                                                Закрыть
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form__item">
                                    <button class="button button_primary" data-object-filter-set>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M16.9697 16.9697C17.2626 16.6768 17.7374 16.6768 18.0303 16.9697L22.5303 21.4697C22.8232 21.7626 22.8232 22.2374 22.5303 22.5303C22.2374 22.8232 21.7626 22.8232 21.4697 22.5303L16.9697 18.0303C16.6768 17.7374 16.6768 17.2626 16.9697 16.9697Z"
                                                  fill="white"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M1.25 11C1.25 5.61522 5.61522 1.25 11 1.25C16.3848 1.25 20.75 5.61522 20.75 11C20.75 16.3848 16.3848 20.75 11 20.75C5.61522 20.75 1.25 16.3848 1.25 11ZM11 2.75C6.44365 2.75 2.75 6.44365 2.75 11C2.75 15.5563 6.44365 19.25 11 19.25C15.5563 19.25 19.25 15.5563 19.25 11C19.25 6.44365 15.5563 2.75 11 2.75Z"
                                                  fill="white"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <? if ($allCount > 0) : ?>
                        <section class="section section_room" id="rooms-anchor">
                            <?
                            $APPLICATION->IncludeComponent(
                                "naturalist:empty",
                                "object_rooms",
                                array(
                                    "VARS" => array(
                                        "arSection" => $arSection,
                                        "arElements" => $arElements,
                                        "daysRange" => $daysRange,
                                        "guests" => $guests,
                                        "children" => $children,
                                        "guestsDeclension" => $guestsDeclension,
                                        "childrenDeclension" => $childrenDeclension,
                                        "arServices" => $arServices,
                                        "arHLRoomFeatures" => $arHLRoomFeatures,
                                        "arHLFeatures" => $arHLFeatures,
                                        "arExternalInfo" => $arExternalInfo,
                                        "dateFrom" => $dateFrom,
                                        "dateTo" => $dateTo,
                                        "page" => $page,
                                        "pageCount" => $pageCount,
                                        "daysDeclension" => $daysDeclension,
                                        "daysCount" => $daysCount,
                                        "arElementsParent" => $arElementsParent ?? [],
                                        'roomsDeclension' => $roomsDeclension,
                                        'bedsDeclension' => $bedsDeclension,
                                        'searchError' => $searchError,
                                    )
                                )
                            );
                            ?>
                        </section>
                        <!-- section-->
                    <? else : ?>
                        <p class="search-error"
                           style="display: none"><?= $searchError != '' ? $searchError : 'Не найдено номеров на выбранные даты' ?></p>
                    <? endif; ?>
                    <div class="object-hero__description">
                        <? if ($arSection['~UF_LIVING_RULES']) { ?>
                            <div class="object__living-rules" id="rules-anchor">
                                <div class="object__living-rules-title">Правила проживания</div>
                                <?= htmlspecialchars_decode($arSection['~UF_LIVING_RULES']) ?>
                            </div>
                        <? } ?>
                        <? if ($arObjectComforts) { ?>
                            <div class="object__living-rules object__comforts" id="comfort-anchor">
                                <div class="object__living-rules-title">Удобства</div>
                                <ul class="object__comforts-list">
                                    <? foreach ($arObjectComforts as $comfort) { ?>
                                        <li>
                                            <? if ($comfort['ELEMENT']) { ?>
                                                <a class="getDetail" href="#"
                                                   detailId="<?= $comfort['ELEMENT'] ?>"><?= $comfort['UF_NAME'] ?></a>
                                            <? } else {
                                                echo $comfort['UF_NAME'];
                                            } ?>
                                        </li>
                                    <? } ?>
                                </ul>
                                <? if (count($arObjectComforts) > 6) {
                                    ?>
                                    <a href="#comfort-more" class="object__comforst-more" data-modal>Показать ещё</a>
                                <? }
                                ?>
                            </div>
                        <? } ?>
                        <? if ($arHLFeatures) { ?>
                            <div class="object__living-rules object__comforts" id="fun-anchor">
                                <div class="object__living-rules-title">Развлечения</div>
                                <ul class="object__comforts-list">
                                    <? foreach ($arHLFeatures as $feat) { ?>
                                        <li>
                                            <? if ($feat['ELEMENT']) { ?>
                                                <a href="#" class="getDetail"
                                                   detailId="<?= $feat['ELEMENT'] ?>"><?= $feat['UF_NAME'] ?></a>
                                            <? } else {
                                                echo $feat['UF_NAME'];
                                            } ?>
                                        </li>
                                    <? } ?>
                                </ul>
                                <? if (count($arHLFeatures) > 6) {
                                    ?>
                                    <a href="#feature-more" class="object__comforst-more" data-modal>Показать ещё</a>
                                <? }
                                ?>
                            </div>
                        <? } ?>
                    </div>
                    <? if ($coords && $isMobile): ?>
                        <div class="about__map mobile">
                            <div class="about__map-heading">
                                <a href="https://yandex.ru/maps/?mode=routes&rtext=~<?= $coords ?>" target="_blank"
                                   class="about__map-route">Проложить маршрут</a>
                                <a class="about__map-big" href="#modal-map" data-modal>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                         fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M15.7727 2.22725C15.9924 2.44692 15.9924 2.80308 15.7727 3.02275L10.5227 8.27275C10.3031 8.49242 9.94692 8.49242 9.72725 8.27275C9.50758 8.05308 9.50758 7.69692 9.72725 7.47725L14.9773 2.22725C15.1969 2.00758 15.5531 2.00758 15.7727 2.22725Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M8.27275 9.72725C8.49242 9.94692 8.49242 10.3031 8.27275 10.5227L3.02275 15.7727C2.80308 15.9924 2.44692 15.9924 2.22725 15.7727C2.00758 15.5531 2.00758 15.1969 2.22725 14.9773L7.47725 9.72725C7.69692 9.50758 8.05308 9.50758 8.27275 9.72725Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M15.4752 1.78324C15.6233 1.82809 15.8019 1.90575 15.9481 2.0519C16.0942 2.19804 16.1719 2.37665 16.2167 2.52476C16.2631 2.67772 16.2873 2.84168 16.2999 2.99943C16.3251 3.31432 16.3091 3.6739 16.2811 4.01291C16.255 4.3288 16.2164 4.64881 16.1833 4.9236C16.1806 4.94649 16.1778 4.96907 16.1752 4.99131C16.1385 5.29658 16.1145 5.51143 16.1128 5.63298C16.1084 5.94361 15.853 6.19184 15.5424 6.18743C15.2317 6.18302 14.9835 5.92762 14.9879 5.61699C14.9907 5.42189 15.0245 5.13709 15.0582 4.85709C15.0608 4.83559 15.0634 4.81395 15.066 4.79217C15.0995 4.51399 15.1356 4.21413 15.1599 3.92026C15.1863 3.60019 15.1964 3.31246 15.1785 3.08924C15.17 2.98312 15.1563 2.90745 15.1421 2.85788C15.0925 2.84367 15.0169 2.82998 14.9107 2.82148C14.6875 2.8036 14.3998 2.81365 14.0797 2.8401C13.7859 2.86438 13.486 2.90049 13.2078 2.93398C13.186 2.93661 13.1644 2.93921 13.1429 2.94179C12.8629 2.97544 12.5781 3.00931 12.383 3.01209C12.0724 3.0165 11.817 2.76826 11.8126 2.45764C11.8081 2.14701 12.0564 1.89161 12.367 1.8872C12.4886 1.88547 12.7034 1.86151 13.0087 1.82483C13.0309 1.82216 13.0535 1.81944 13.0764 1.81668C13.3512 1.78358 13.6712 1.74502 13.9871 1.71892C14.3261 1.69091 14.6857 1.67485 15.0006 1.70007C15.1583 1.7127 15.3223 1.73692 15.4752 1.78324Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M2.45764 11.8126C2.76826 11.817 3.0165 12.0724 3.01209 12.383C3.00931 12.5781 2.97544 12.8629 2.94179 13.1429C2.93921 13.1644 2.93661 13.186 2.93398 13.2078C2.90049 13.486 2.86438 13.7859 2.8401 14.0797C2.81365 14.3998 2.8036 14.6875 2.82148 14.9107C2.82998 15.0169 2.84367 15.0925 2.85788 15.1421C2.90745 15.1563 2.98312 15.17 3.08924 15.1785C3.31246 15.1964 3.60019 15.1863 3.92026 15.1599C4.21413 15.1356 4.51399 15.0995 4.79218 15.066C4.81395 15.0634 4.83559 15.0608 4.85709 15.0582C5.13709 15.0245 5.42189 14.9907 5.617 14.9879C5.92762 14.9835 6.18302 15.2317 6.18743 15.5424C6.19184 15.853 5.94361 16.1084 5.63298 16.1128C5.51143 16.1145 5.29658 16.1385 4.99131 16.1752C4.96907 16.1778 4.94649 16.1806 4.9236 16.1833C4.64881 16.2164 4.3288 16.255 4.01291 16.2811C3.6739 16.3091 3.31432 16.3251 2.99943 16.2999C2.84168 16.2873 2.67772 16.2631 2.52476 16.2167C2.37665 16.1719 2.19804 16.0942 2.0519 15.9481C1.90575 15.8019 1.82809 15.6233 1.78324 15.4752C1.73692 15.3223 1.7127 15.1583 1.70007 15.0006C1.67485 14.6857 1.69091 14.3261 1.71892 13.9871C1.74502 13.6712 1.78357 13.3512 1.81668 13.0764C1.81944 13.0535 1.82216 13.0309 1.82483 13.0087C1.86151 12.7034 1.88547 12.4886 1.8872 12.367C1.89161 12.0564 2.14701 11.8081 2.45764 11.8126ZM2.87302 15.1845C2.87302 15.1845 2.87292 15.1843 2.87273 15.1839L2.87302 15.1845Z"
                                              fill="white"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="about__map-map">
                                <div id="map-preview"></div>
                            </div>
                        </div>
                    <? endif; ?>
                    <? if ($reviewsCount > 0): ?>
                        <div class="reviews__scors mobile">
                            <div class="reviews__title">Что нравится гостям</div>
                            <ul class="list list_score">
                                <li class="list__item">
                                    <div class="list__item-label">Удобство расположения</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[1][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[1][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Питание</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[2][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[2][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Уют</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[3][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[3][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Сервис</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[4][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[4][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Чистота</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[5][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[5][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Эстетика окружения</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[6][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[6][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Разнообразие досуга</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[7][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[7][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Цена/качество</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[8][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[8][0] ?? "0.0" ?></div>
                                </li>
                            </ul>
                        </div>
                    <? endif; ?>
                    <? if ($arReviews) : ?>
                        <section class="section section_reviews" id="reviews-anchor">
                            <?
                            $APPLICATION->IncludeComponent(
                                "naturalist:empty",
                                "object_reviews",
                                array(
                                    "avgRating" => $avgRating,
                                    "reviewsDeclension" => $reviewsDeclension,
                                    "reviewsCount" => $reviewsCount,
                                    "arAvgCriterias" => $arAvgCriterias,
                                    "reviewsSortType" => $reviewsSortType,
                                    "arReviews" => $arReviews,
                                    "arReviewsLikesData" => $arReviewsLikesData,
                                    "arReviewsUsers" => $arReviewsUsers,
                                    "reviewsPage" => $reviewsPage,
                                    "reviewsPageCount" => $reviewsPageCount,
                                    "sectionId" => $arSection['ID'],
                                    "isUserReview" => $isUserReview,
                                )
                            );
                            ?>
                        </section>
                    <? endif; ?>
                </div>
                <div class="object__info-right" id="map">
                    <? if ($arSection['STORIES']) { ?>
                        <div class="object__stories_wrapper">
                            <div id="object__stories"></div>

                            <? if (count($arSection["STORIES"]) > 3) : ?>
                                <div class="object__stories-button-prev" id="object__stories-prev">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38"
                                         fill="none">
                                        <g filter="url(#filter0_b_6845_59178)">
                                            <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M17.5752 14.0495C17.7949 14.2692 17.7949 14.6253 17.5752 14.845L13.4205 18.9998L17.5752 23.1545C17.7949 23.3742 17.7949 23.7303 17.5752 23.95C17.3556 24.1697 16.9994 24.1697 16.7798 23.95L12.2273 19.3975C12.1218 19.292 12.0625 19.149 12.0625 18.9998C12.0625 18.8506 12.1218 18.7075 12.2273 18.602L16.7798 14.0495C16.9994 13.8298 17.3556 13.8298 17.5752 14.0495Z"
                                                  fill="white"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M12.1899 19C12.1899 18.6893 12.4418 18.4375 12.7524 18.4375H25.3749C25.6856 18.4375 25.9374 18.6893 25.9374 19C25.9374 19.3107 25.6856 19.5625 25.3749 19.5625H12.7524C12.4418 19.5625 12.1899 19.3107 12.1899 19Z"
                                                  fill="white"/>
                                        </g>
                                        <defs>
                                            <filter id="filter0_b_6845_59178" x="-12" y="-12" width="62" height="62"
                                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6"/>
                                                <feComposite in2="SourceAlpha" operator="in"
                                                             result="effect1_backgroundBlur_6845_59178"/>
                                                <feBlend mode="normal" in="SourceGraphic"
                                                         in2="effect1_backgroundBlur_6845_59178" result="shape"/>
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="object__stories-button-next" id="object__stories-next">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38"
                                         fill="none">
                                        <g filter="url(#filter0_b_6845_59184)">
                                            <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M20.4245 14.0495C20.6442 13.8298 21.0003 13.8298 21.22 14.0495L25.7725 18.602C25.878 18.7075 25.9373 18.8506 25.9373 18.9998C25.9373 19.149 25.878 19.292 25.7725 19.3975L21.22 23.95C21.0003 24.1697 20.6442 24.1697 20.4245 23.95C20.2048 23.7303 20.2048 23.3742 20.4245 23.1545L24.5793 18.9998L20.4245 14.845C20.2048 14.6253 20.2048 14.2692 20.4245 14.0495Z"
                                                  fill="white"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M12.0625 19C12.0625 18.6893 12.3143 18.4375 12.625 18.4375H25.2475C25.5582 18.4375 25.81 18.6893 25.81 19C25.81 19.3107 25.5582 19.5625 25.2475 19.5625H12.625C12.3143 19.5625 12.0625 19.3107 12.0625 19Z"
                                                  fill="white"/>
                                        </g>
                                        <defs>
                                            <filter id="filter0_b_6845_59184" x="-12" y="-12" width="62" height="62"
                                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6"/>
                                                <feComposite in2="SourceAlpha" operator="in"
                                                             result="effect1_backgroundBlur_6845_59184"/>
                                                <feBlend mode="normal" in="SourceGraphic"
                                                         in2="effect1_backgroundBlur_6845_59184" result="shape"/>
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                            <? endif; ?>
                        </div>

                        <script>
                            const objectStories = '<?= \Bitrix\Main\Web\Json::encode($arSection['STORIES']) ?>'
                        </script>
                    <? } ?>
                    <? if ($coords): ?>
                        <div class="about__map">
                            <div class="about__map-heading">
                                <a href="https://yandex.ru/maps/?mode=routes&rtext=~<?= $coords ?>" target="_blank"
                                   class="about__map-route">Проложить маршрут</a>
                                <a class="about__map-big" href="#modal-map" data-modal>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                         fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M15.7727 2.22725C15.9924 2.44692 15.9924 2.80308 15.7727 3.02275L10.5227 8.27275C10.3031 8.49242 9.94692 8.49242 9.72725 8.27275C9.50758 8.05308 9.50758 7.69692 9.72725 7.47725L14.9773 2.22725C15.1969 2.00758 15.5531 2.00758 15.7727 2.22725Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M8.27275 9.72725C8.49242 9.94692 8.49242 10.3031 8.27275 10.5227L3.02275 15.7727C2.80308 15.9924 2.44692 15.9924 2.22725 15.7727C2.00758 15.5531 2.00758 15.1969 2.22725 14.9773L7.47725 9.72725C7.69692 9.50758 8.05308 9.50758 8.27275 9.72725Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M15.4752 1.78324C15.6233 1.82809 15.8019 1.90575 15.9481 2.0519C16.0942 2.19804 16.1719 2.37665 16.2167 2.52476C16.2631 2.67772 16.2873 2.84168 16.2999 2.99943C16.3251 3.31432 16.3091 3.6739 16.2811 4.01291C16.255 4.3288 16.2164 4.64881 16.1833 4.9236C16.1806 4.94649 16.1778 4.96907 16.1752 4.99131C16.1385 5.29658 16.1145 5.51143 16.1128 5.63298C16.1084 5.94361 15.853 6.19184 15.5424 6.18743C15.2317 6.18302 14.9835 5.92762 14.9879 5.61699C14.9907 5.42189 15.0245 5.13709 15.0582 4.85709C15.0608 4.83559 15.0634 4.81395 15.066 4.79217C15.0995 4.51399 15.1356 4.21413 15.1599 3.92026C15.1863 3.60019 15.1964 3.31246 15.1785 3.08924C15.17 2.98312 15.1563 2.90745 15.1421 2.85788C15.0925 2.84367 15.0169 2.82998 14.9107 2.82148C14.6875 2.8036 14.3998 2.81365 14.0797 2.8401C13.7859 2.86438 13.486 2.90049 13.2078 2.93398C13.186 2.93661 13.1644 2.93921 13.1429 2.94179C12.8629 2.97544 12.5781 3.00931 12.383 3.01209C12.0724 3.0165 11.817 2.76826 11.8126 2.45764C11.8081 2.14701 12.0564 1.89161 12.367 1.8872C12.4886 1.88547 12.7034 1.86151 13.0087 1.82483C13.0309 1.82216 13.0535 1.81944 13.0764 1.81668C13.3512 1.78358 13.6712 1.74502 13.9871 1.71892C14.3261 1.69091 14.6857 1.67485 15.0006 1.70007C15.1583 1.7127 15.3223 1.73692 15.4752 1.78324Z"
                                              fill="white"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M2.45764 11.8126C2.76826 11.817 3.0165 12.0724 3.01209 12.383C3.00931 12.5781 2.97544 12.8629 2.94179 13.1429C2.93921 13.1644 2.93661 13.186 2.93398 13.2078C2.90049 13.486 2.86438 13.7859 2.8401 14.0797C2.81365 14.3998 2.8036 14.6875 2.82148 14.9107C2.82998 15.0169 2.84367 15.0925 2.85788 15.1421C2.90745 15.1563 2.98312 15.17 3.08924 15.1785C3.31246 15.1964 3.60019 15.1863 3.92026 15.1599C4.21413 15.1356 4.51399 15.0995 4.79218 15.066C4.81395 15.0634 4.83559 15.0608 4.85709 15.0582C5.13709 15.0245 5.42189 14.9907 5.617 14.9879C5.92762 14.9835 6.18302 15.2317 6.18743 15.5424C6.19184 15.853 5.94361 16.1084 5.63298 16.1128C5.51143 16.1145 5.29658 16.1385 4.99131 16.1752C4.96907 16.1778 4.94649 16.1806 4.9236 16.1833C4.64881 16.2164 4.3288 16.255 4.01291 16.2811C3.6739 16.3091 3.31432 16.3251 2.99943 16.2999C2.84168 16.2873 2.67772 16.2631 2.52476 16.2167C2.37665 16.1719 2.19804 16.0942 2.0519 15.9481C1.90575 15.8019 1.82809 15.6233 1.78324 15.4752C1.73692 15.3223 1.7127 15.1583 1.70007 15.0006C1.67485 14.6857 1.69091 14.3261 1.71892 13.9871C1.74502 13.6712 1.78357 13.3512 1.81668 13.0764C1.81944 13.0535 1.82216 13.0309 1.82483 13.0087C1.86151 12.7034 1.88547 12.4886 1.8872 12.367C1.89161 12.0564 2.14701 11.8081 2.45764 11.8126ZM2.87302 15.1845C2.87302 15.1845 2.87292 15.1843 2.87273 15.1839L2.87302 15.1845Z"
                                              fill="white"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="about__map-map">
                                <div id="map-preview"></div>
                            </div>
                        </div>
                    <? endif; ?>
                    <? if ($reviewsCount > 0): ?>
                        <div class="object-hero__reviews-right">
                            <div class="object-hero__reviews-title">Отзывы</div>
                            <div class="object-hero__reviews-info">
                                <a href="#reviews-anchor" data-scroll-to class="score">
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <?= $avgRating ?>
                                </a>
                                <span class="map-ellips"></span>
                                <a href="#reviews-anchor"
                                   data-scroll-to=""><?= $reviewsCount ?> <?= $reviewsDeclension->get($reviewsCount) ?>
                                </a>
                            </div>
                        </div>
                        <div class="reviews__scors">
                            <div class="reviews__title">Что нравится гостям</div>
                            <ul class="list list_score">
                                <li class="list__item">
                                    <div class="list__item-label">Удобство расположения</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[1][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[1][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Питание</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[2][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[2][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Уют</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[3][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[3][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Сервис</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[4][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[4][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Чистота</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[5][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[5][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Эстетика окружения</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[6][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[6][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Разнообразие досуга</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[7][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[7][0] ?? "0.0" ?></div>
                                </li>
                                <li class="list__item">
                                    <div class="list__item-label">Цена/качество</div>
                                    <div class="list__item-progress">
                                        <div style="width: <?= $arAvgCriterias[8][1] ?? 0 ?>%"></div>
                                    </div>
                                    <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/star-score.svg" alt="">
                                    <div class="list__item-number"><?= $arAvgCriterias[8][0] ?? "0.0" ?></div>
                                </li>
                            </ul>
                        </div>
                    <? endif; ?>
                </div>
            </div>


            <? /*<div class="object-hero__heading">
                    <div class="object-hero__controls">
                        <div class="share">
                            <button class="share__control" type="button" data-share="data-share">
                                <svg class="icon icon_share" viewbox="0 0 16 16" style="width: 1.6rem; height: 1.6rem;">
                                    <use xlink:href="#share" />
                                </svg>
                            </button>
                            <div class="share__dropdown">
                                <div class="share__copy">
                                    <button type="button" data-copy-link="<?= $currentURL ?>">Копировать ссылку</button>
                                </div>
                                <ul class="list">
                                    <li class="list__item">
                                        <a class="list__link"
                                            href="https://t.me/share/url?url=<?= $currentURL ?>&text=<?= $arSection["NAME"] ?>"
                                            target="_blank">
                                            <span class="list__item-icon">
                                                <svg class="icon icon_telegram" viewbox="0 0 16 16"
                                                    style="width: 1.6rem; height: 1.6rem;">
                                                    <use xlink:href="#telegram" />
                                                </svg>
                                            </span>
                                            <span class="list__item-title">Telegram</span>
                                        </a>
                                    </li>
                                    <li class="list__item">
                                        <a class="list__link" href="https://vk.com/share.php?url=<?= $currentURL ?>"
                                            target="_blank">
                                            <span class="list__item-icon">
                                                <svg class="icon icon_vk" viewbox="0 0 22 12"
                                                    style="width: 2.2rem; height: 1.2rem;">
                                                    <use xlink:href="#vk" />
                                                </svg>
                                            </span>
                                            <span class="list__item-title">Вконтакте</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>*/ ?>

        </div>
    </div>
</section>

<script>
    dataLayer.push({
        "ecommerce": {
            "currencyCode": "RUB",
            "detail": {
                "products": [{
                    "id": "<?= $arSection['UF_EXTERNAL_ID'] ?>",
                    "name": "<?= $arSection['NAME'] ?>",
                    "price": <?= $arSection['UF_MIN_PRICE'] ?>,
                    "brand": "<?= $arSection['UF_EXTERNAL_SERVICE'] ?>",
                }]
            }
        }
    });
</script>