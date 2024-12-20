<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Промо сертификаты");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/index.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/assets/css/promo-cert.css');
$APPLICATION->SetAdditionalCSS('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css');
$APPLICATION->AddHeadScript('https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js');
$APPLICATION->AddHeadScript('https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/assets/js/promo-cert.js');

use Naturalist\Certificates\CatalogHelper;

// Картинки сертификатов
$certificates = new CatalogHelper;
$variants = $certificates->hlElVariantsValues;

?>
<div class="certs__container">
    <section class="certs__top-banner">
        <div class="certs__top-banner-inner">
            <p class="certs__top-banner-text1">Подарите близким <br>незабываемый <br>отдых на природе</p>
            <p class="certs__top-banner-text2">Получатель сертификата <br>сам выберет отель и дату</p>
            <a href="/certificates/buy/" class="certs__top-banner-link">Узнать подробнее</a>
        </div>
    </section>
    <section class="certs__elec">
        <p class="certs__elec-title">
            Более 300 объектов по всей России <br>в подарочных сертификатах с кастомным дизайном
        </p>
        <div class="certs__elec-list-wrap">
            <div class="certs__elec-list">
                <? foreach ($variants as $variant) { ?>
                    <div class="carts__elec-item">
                        <img width="339" src="<?= CFile::getPath($variant['UF_FILE']) ?>" alt="">
                    </div>
                <? } ?>
            </div>
        </div>
        <a href="/certificates/buy/" class="certs__elec-link">Выбрать сертификат</a>
    </section>
    <section class="certs__catalog">
        <?
        global $arOffersFilter;
        $arOffersFilter = array();

        $tab = $_REQUEST['tab'] ?? '';
        if ($tab == 'top') {
            $arOffersFilter = array(
                'UF_TOP' => 1
            );
        }
        if ($tab == 'action') {
            $arOffersFilter = array(
                '!UF_ACTION' => ''
            );
        }
        if ($tab == 'premium') {
            $arOffersFilter = array(
                'UF_PREMIUM' => 1
            );
        }
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section.list",
            "main_offers",
            array(
                "VIEW_MODE" => "TEXT",
                "SHOW_PARENT_NAME" => "Y",
                "IBLOCK_TYPE" => "",
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "SECTION_ID" => "",
                "SECTION_CODE" => "",
                "SECTION_URL" => "",
                "FILTER_NAME" => "arOffersFilter",
                "COUNT_ELEMENTS" => "Y",
                "TOP_DEPTH" => "1",
                "SECTION_FIELDS" => "",
                "SECTION_USER_FIELDS" => array("UF_*"),
                "ADD_SECTIONS_CHAIN" => "Y",
                "CACHE_TYPE" => "N",
                "CACHE_TIME" => "36000000",
                "CACHE_NOTES" => "",
                "CACHE_GROUPS" => "N",
                "ITEMS_COUNT" => 6,
                "TABS" => $arTabs,
                "SHOW_MORE_LINK" => "/catalog/",
            )
        );
        ?>
    </section>
    <section class="certs__sliders">
        <div class="sliders__tabs">
            <button type="button" class="button sliders__switcher active" slider="box">Подарочная коробка</button>
            <button type="button" class="button sliders__switcher" slider="elec">Электронный сертификат</button>
        </div>
        <div class="sliders__list">
            <div class="sliders__one active" id="box">
                <div class="swiper certs__swiper-one">
                    <div class="swiper-wrapper mob-wrap">
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Глубокий океан</span>
                                <img src="img/fiz/1.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Мятный</span>
                                <img src="img/fiz/2.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Шоколадный</span>
                                <img src="img/fiz/3.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Лавандовый</span>
                                <img src="img/fiz/4.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Кварцево-серый</span>
                                <img src="img/fiz/5.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Алый</span>
                                <img src="img/fiz/6.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Темно-зеленый</span>
                                <img src="img/fiz/7.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Серебристо-серый</span>
                                <img src="img/fiz/8.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <span class="sliders__slide-title">Пыльно-розовый</span>
                                <img src="img/fiz/9.jpg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-button-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                        <g filter="url(#filter0_b_5407_68662)">
                            <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5752 14.0495C17.7949 14.2692 17.7949 14.6253 17.5752 14.845L13.4205 18.9998L17.5752 23.1545C17.7949 23.3742 17.7949 23.7303 17.5752 23.95C17.3556 24.1697 16.9994 24.1697 16.7798 23.95L12.2273 19.3975C12.1218 19.292 12.0625 19.149 12.0625 18.9998C12.0625 18.8506 12.1218 18.7075 12.2273 18.602L16.7798 14.0495C16.9994 13.8298 17.3556 13.8298 17.5752 14.0495Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.19 19C12.19 18.6893 12.4418 18.4375 12.7525 18.4375H25.375C25.6857 18.4375 25.9375 18.6893 25.9375 19C25.9375 19.3107 25.6857 19.5625 25.375 19.5625H12.7525C12.4418 19.5625 12.19 19.3107 12.19 19Z" fill="white" />
                        </g>
                        <defs>
                            <filter id="filter0_b_5407_68662" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_5407_68662" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_5407_68662" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
                <div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                        <g filter="url(#filter0_b_5407_68669)">
                            <rect x="38" y="38" width="38" height="38" rx="19" transform="rotate(-180 38 38)" fill="black" fill-opacity="0.6" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4248 23.9505C20.2051 23.7308 20.2051 23.3747 20.4248 23.155L24.5795 19.0002L20.4248 14.8455C20.2051 14.6258 20.2051 14.2697 20.4248 14.05C20.6444 13.8303 21.0006 13.8303 21.2202 14.05L25.7727 18.6025C25.8782 18.708 25.9375 18.851 25.9375 19.0002C25.9375 19.1494 25.8782 19.2925 25.7727 19.398L21.2202 23.9505C21.0006 24.1702 20.6444 24.1702 20.4248 23.9505Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M25.8101 19C25.8101 19.3107 25.5582 19.5625 25.2476 19.5625L12.6251 19.5625C12.3144 19.5625 12.0626 19.3107 12.0626 19C12.0626 18.6893 12.3144 18.4375 12.6251 18.4375L25.2476 18.4375C25.5582 18.4375 25.8101 18.6893 25.8101 19Z" fill="white" />
                        </g>
                        <defs>
                            <filter id="filter0_b_5407_68669" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_5407_68669" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_5407_68669" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
                <p class="sliders__text">Оформите подарок так, чтобы он запомнился и выберите одну из 15 кастомных упаковок. Сертификат действителен в течение года после покупки.</p>
            </div>
            <div class="sliders__one" id="elec">
                <div class="swiper certs__swiper-two">
                    <div class="swiper-wrapper mob-wrap">
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <img src="img/elec/1.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <img src="img/elec/2.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <img src="img/elec/3.jpg" alt="">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="swiper-inner">
                                <img src="img/elec/4.jpg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-button-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                        <g filter="url(#filter0_b_5407_68662)">
                            <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5752 14.0495C17.7949 14.2692 17.7949 14.6253 17.5752 14.845L13.4205 18.9998L17.5752 23.1545C17.7949 23.3742 17.7949 23.7303 17.5752 23.95C17.3556 24.1697 16.9994 24.1697 16.7798 23.95L12.2273 19.3975C12.1218 19.292 12.0625 19.149 12.0625 18.9998C12.0625 18.8506 12.1218 18.7075 12.2273 18.602L16.7798 14.0495C16.9994 13.8298 17.3556 13.8298 17.5752 14.0495Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.19 19C12.19 18.6893 12.4418 18.4375 12.7525 18.4375H25.375C25.6857 18.4375 25.9375 18.6893 25.9375 19C25.9375 19.3107 25.6857 19.5625 25.375 19.5625H12.7525C12.4418 19.5625 12.19 19.3107 12.19 19Z" fill="white" />
                        </g>
                        <defs>
                            <filter id="filter0_b_5407_68662" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_5407_68662" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_5407_68662" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
                <div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                        <g filter="url(#filter0_b_5407_68669)">
                            <rect x="38" y="38" width="38" height="38" rx="19" transform="rotate(-180 38 38)" fill="black" fill-opacity="0.6" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4248 23.9505C20.2051 23.7308 20.2051 23.3747 20.4248 23.155L24.5795 19.0002L20.4248 14.8455C20.2051 14.6258 20.2051 14.2697 20.4248 14.05C20.6444 13.8303 21.0006 13.8303 21.2202 14.05L25.7727 18.6025C25.8782 18.708 25.9375 18.851 25.9375 19.0002C25.9375 19.1494 25.8782 19.2925 25.7727 19.398L21.2202 23.9505C21.0006 24.1702 20.6444 24.1702 20.4248 23.9505Z" fill="white" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M25.8101 19C25.8101 19.3107 25.5582 19.5625 25.2476 19.5625L12.6251 19.5625C12.3144 19.5625 12.0626 19.3107 12.0626 19C12.0626 18.6893 12.3144 18.4375 12.6251 18.4375L25.2476 18.4375C25.5582 18.4375 25.8101 18.6893 25.8101 19Z" fill="white" />
                        </g>
                        <defs>
                            <filter id="filter0_b_5407_68669" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_5407_68669" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_5407_68669" result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
                <p class="sliders__text">Электронное письмо поментально придет на вашу почту. А также вы можете указать почту получателя и он сразу получит. Сертификат действителен в течение года после покупки.</p>
            </div>
        </div>
        <a href="/certificates/buy/" class="certs__elec-link">Купить сертификат</a>
    </section>
    <section class="certs__video">
        <p class="video__title">Отзывы</p>
        <div class="video__one" id="video">
            <div class="swiper certs__video-slider">
                <div class="swiper-wrapper video-mob-wrap">
                    <div class="swiper-slide">
                        <a href="img/video/1.mp4" class="video__slider-inner fancy" data-fancybox='video-galery' data-caption="Инна Мишка&lt;br /&gt;Модель, блогер">
                            <img width="338" src="img/preview/1.jpg" alt="">
                            <span class="video__slide-text">
                                <span class="video__slide-name">Инна Мишка</span>
                                <span class="video__slide-prof">Модель, блогер</span>
                            </span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="img/video/2.mp4" class="video__slider-inner fancy" data-fancybox='video-galery' data-caption="Анна Хинкевич&lt;br /&gt;Российская актриса кино &lt;br /&gt;и телевидения, ведущая">
                            <img width="338" src="img/preview/2.jpg" alt="">
                            <span class="video__slide-text">
                                <span class="video__slide-name">Анна Хинкевич</span>
                                <span class="video__slide-prof">Российская актриса кино <br>и телевидения, ведущая</span>
                            </span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="img/video/3.mp4" class="video__slider-inner fancy" data-fancybox='video-galery' data-caption="Роман Кокорин&lt;br /&gt;Тревел фотограф">
                            <img width="338" src="img/preview/3.jpg" alt="">
                            <span class="video__slide-text">
                                <span class="video__slide-name">Роман Кокорин</span>
                                <span class="video__slide-prof">Тревел фотограф</span>
                            </span>
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="img/video/4.mp4" class="video__slider-inner fancy" data-fancybox='video-galery' data-caption="Анатолий Цой&lt;br /&gt;Артист">
                            <img width="338" src="img/preview/4.jpg" alt="">
                            <span class="video__slide-text">
                                <span class="video__slide-name">Анатолий Цой</span>
                                <span class="video__slide-prof">Артист</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <button class="button video-more">Показать ещё</button>
            <div class="swiper-button-prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                    <g filter="url(#filter0_b_5407_68662)">
                        <rect width="38" height="38" rx="19" fill="black" fill-opacity="0.6" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5752 14.0495C17.7949 14.2692 17.7949 14.6253 17.5752 14.845L13.4205 18.9998L17.5752 23.1545C17.7949 23.3742 17.7949 23.7303 17.5752 23.95C17.3556 24.1697 16.9994 24.1697 16.7798 23.95L12.2273 19.3975C12.1218 19.292 12.0625 19.149 12.0625 18.9998C12.0625 18.8506 12.1218 18.7075 12.2273 18.602L16.7798 14.0495C16.9994 13.8298 17.3556 13.8298 17.5752 14.0495Z" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.19 19C12.19 18.6893 12.4418 18.4375 12.7525 18.4375H25.375C25.6857 18.4375 25.9375 18.6893 25.9375 19C25.9375 19.3107 25.6857 19.5625 25.375 19.5625H12.7525C12.4418 19.5625 12.19 19.3107 12.19 19Z" fill="white" />
                    </g>
                    <defs>
                        <filter id="filter0_b_5407_68662" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                            <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_5407_68662" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_5407_68662" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </div>
            <div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" fill="none">
                    <g filter="url(#filter0_b_5407_68669)">
                        <rect x="38" y="38" width="38" height="38" rx="19" transform="rotate(-180 38 38)" fill="black" fill-opacity="0.6" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.4248 23.9505C20.2051 23.7308 20.2051 23.3747 20.4248 23.155L24.5795 19.0002L20.4248 14.8455C20.2051 14.6258 20.2051 14.2697 20.4248 14.05C20.6444 13.8303 21.0006 13.8303 21.2202 14.05L25.7727 18.6025C25.8782 18.708 25.9375 18.851 25.9375 19.0002C25.9375 19.1494 25.8782 19.2925 25.7727 19.398L21.2202 23.9505C21.0006 24.1702 20.6444 24.1702 20.4248 23.9505Z" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M25.8101 19C25.8101 19.3107 25.5582 19.5625 25.2476 19.5625L12.6251 19.5625C12.3144 19.5625 12.0626 19.3107 12.0626 19C12.0626 18.6893 12.3144 18.4375 12.6251 18.4375L25.2476 18.4375C25.5582 18.4375 25.8101 18.6893 25.8101 19Z" fill="white" />
                    </g>
                    <defs>
                        <filter id="filter0_b_5407_68669" x="-12" y="-12" width="62" height="62" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feGaussianBlur in="BackgroundImageFix" stdDeviation="6" />
                            <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_5407_68669" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_5407_68669" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </div>
        </div>
    </section>
    <section class="certs__banner2">
        <div class="certs__banner2-inner">
            <p class="certs__banner2-text1"><svg xmlns="http://www.w3.org/2000/svg" width="218" height="21" viewBox="0 0 218 21" fill="none">
                    <path d="M43.5282 8.98294V2.98926H45.6565V17.2115H43.5282V10.772H31.7333V17.2115H29.605V2.98926H31.7333V8.98294H43.5282Z" fill="white" />
                    <path d="M66.6332 17.2124H64.376L62.9101 14.3451H52.8756L51.4035 17.2124H49.1464L56.6539 2.99023H59.1134L66.6209 17.2124H66.6332ZM53.7465 12.6584H62.0453L58.7147 6.21899C58.2608 5.32747 58.0339 4.67088 58.0339 4.24921V4.23114H57.764V4.24921C57.764 4.67088 57.5371 5.32747 57.0832 6.21899L53.7527 12.6584H53.7465Z" fill="white" />
                    <path d="M75.7171 17.2124H73.5888V4.7793H67.032V2.99023H82.28V4.7793H75.7232V17.2124H75.7171Z" fill="white" />
                    <path d="M98 2.99235H100.46L93.2158 14.2689C92.5105 15.3652 91.7806 16.1724 91.02 16.6845C90.2595 17.1965 89.3088 17.4555 88.1679 17.4555C87.5178 17.4555 86.9167 17.3651 86.3708 17.1905V15.3592C86.9228 15.4978 87.438 15.564 87.9226 15.564C88.7199 15.564 89.3824 15.4315 89.8976 15.1604C90.4128 14.8894 90.8667 14.4496 91.2531 13.8412L91.4984 13.4376L84.2179 2.99235H86.7204L91.6456 10.3053C91.9217 10.7269 92.1302 11.0823 92.2774 11.3715C92.4246 11.6606 92.4921 11.9196 92.4921 12.1305V12.2509H92.762V12.1305C92.762 11.9016 92.8294 11.6425 92.9582 11.3474C93.087 11.0582 93.2772 10.7088 93.5287 10.2992L98 2.98633V2.99235Z" fill="white" />
                    <path d="M112.831 2.99023C114.696 2.99023 116.113 3.40588 117.094 4.23716C118.076 5.06845 118.56 6.20695 118.56 7.64061C118.56 9.07428 118.069 10.2128 117.094 11.0441C116.113 11.8753 114.696 12.291 112.831 12.291H105.882V17.2064H103.754V2.99023H112.837H112.831ZM112.831 10.5079C114.003 10.5079 114.898 10.2489 115.512 9.7369C116.125 9.22487 116.432 8.52611 116.432 7.64664C116.432 6.76716 116.125 6.0684 115.512 5.55638C114.898 5.04435 114.003 4.78533 112.831 4.78533H105.821V10.514H112.831V10.5079Z" fill="white" />
                    <path d="M137.163 17.2124H134.906L133.44 14.3451H123.405L121.933 17.2124H119.676L127.184 2.99023H129.643L137.151 17.2124H137.163ZM124.276 12.6584H132.575L129.245 6.21899C128.791 5.32747 128.564 4.67088 128.564 4.24921V4.23114H128.294V4.24921C128.294 4.67088 128.067 5.32747 127.613 6.21899L124.283 12.6584H124.276Z" fill="white" />
                    <path d="M138.801 17.2365V15.4053C139.101 15.5137 139.426 15.5679 139.776 15.5679C140.451 15.5679 140.966 15.3872 141.316 15.0198C141.665 14.6523 141.892 14.0319 141.99 13.1524L143.149 2.99023H156.723V17.2124H154.595V4.7793H145.039L144.063 13.4777C143.928 14.7788 143.53 15.7667 142.873 16.4414C142.217 17.1161 141.334 17.4594 140.218 17.4594C139.696 17.4594 139.224 17.3871 138.813 17.2365H138.801Z" fill="white" />
                    <path d="M161.661 17.2124V2.99023H163.728V13.0319C163.728 13.6283 163.697 14.1343 163.642 14.5439C163.587 14.9595 163.495 15.4354 163.372 15.9776H163.642L174.566 2.99626H177.915V17.2124H175.848V7.17678C175.848 6.58042 175.872 6.07442 175.933 5.6648C175.989 5.24916 176.081 4.77328 176.203 4.23114H175.933L165.01 17.2124H161.661Z" fill="white" />
                    <path d="M186.403 16.5017C185.011 15.8632 183.95 14.9897 183.226 13.8693C182.502 12.7489 182.141 11.4959 182.141 10.0984C182.141 8.70087 182.502 7.44792 183.226 6.32749C183.95 5.20706 185.011 4.33361 186.403 3.69509C187.796 3.05657 189.47 2.7373 191.433 2.7373C193.267 2.7373 194.837 2.9903 196.15 3.4963C197.462 4.0023 198.493 4.70106 199.241 5.58054C199.995 6.46002 200.486 7.45394 200.719 8.56835H198.591C198.29 7.40575 197.548 6.43592 196.364 5.6709C195.187 4.90587 193.543 4.52035 191.433 4.52035C189.93 4.52035 188.636 4.7613 187.557 5.24923C186.471 5.73716 185.655 6.39978 185.103 7.24311C184.551 8.08042 184.275 9.03218 184.275 10.0863C184.275 11.1405 184.551 12.0923 185.103 12.9296C185.655 13.7669 186.471 14.4355 187.557 14.9235C188.642 15.4114 189.93 15.6523 191.433 15.6523C193.543 15.6523 195.187 15.2728 196.364 14.5018C197.542 13.7368 198.284 12.773 198.591 11.6043H200.719C200.376 13.3633 199.431 14.7789 197.885 15.8391C196.34 16.9053 194.187 17.4354 191.433 17.4354C189.476 17.4354 187.796 17.1161 186.403 16.4776V16.5017Z" fill="white" />
                    <path d="M211.146 17.2124H209.018V4.7793H202.461V2.99023H217.709V4.7793H211.152V17.2124H211.146Z" fill="white" />
                    <path d="M10.8178 0.140625C5.23009 0.140625 0.697388 4.59222 0.697388 10.0799C0.697388 15.5676 5.23009 20.0192 10.8178 20.0192C16.4054 20.0192 20.9381 15.5676 20.9381 10.0799C20.9381 4.59222 16.4116 0.140625 10.8178 0.140625ZM6.19306 14.2303L10.8239 3.94165L15.4547 14.2303H6.19919H6.19306Z" fill="white" />
                </svg>
            </p>
            <p class="certs__banner2-text2">Бронирование на <br>Новогодние праздники!</p>
            <p class="certs__banner2-text3">Успей выбрать лучшее место заранее</p>
            <a href="/catalog/?dateFrom=02.01.2025&dateTo=05.01.2025&guests=2" class="certs__banner2-link">Забронировать</a>
        </div>
    </section>
    <section class="certs__form">
        <? $APPLICATION->IncludeComponent(
            "bitrix:subscribe.form",
            "subscribe-footer",
            array(
                "CACHE_TIME" => "3600",
                "CACHE_TYPE" => "A",
                "PAGE" => "",
                "SHOW_HIDDEN" => "N",
                "USE_PERSONALIZATION" => "N",
                "COMPONENT_TEMPLATE" => "subscribe-footer",
                "FORM_TITLE" => "Подпишитесь на рассылку",
                "FORM_SUBTITLE" => "Будьте в курсе выгодных цен и первыми узнавайте о новых локациях!",
                "FORM_POLITICS_LINK" => "/policy/"
            ),
            false
        ); ?>
    </section>
    <section class="certs__faq">
        <?php
        $APPLICATION->includeComponent(
            'naturalist:certificates.index',
            'promo',
            []
        );
        ?>
    </section>
</div>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>