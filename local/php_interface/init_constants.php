<?php
if (!defined("CATALOG_IBLOCK_ID")) define(("CATALOG_IBLOCK_ID"), 1); // ID ИБ Каталог
if (!defined("NEWS_IBLOCK_ID")) define(("NEWS_IBLOCK_ID"), 3); // ID ИБ Новости
if (!defined("REVIEWS_IBLOCK_ID")) define(("REVIEWS_IBLOCK_ID"), 4); // ID ИБ Отзывы
if (!defined("IMPRESSIONS_IBLOCK_ID")) define(("IMPRESSIONS_IBLOCK_ID"), 5); // ID ИБ Впечатления
if (!defined("SETTINGS_IBLOCK_ID")) define(("SETTINGS_IBLOCK_ID"), 6); // ID ИБ Настройки
if (!defined("ABOUT_IBLOCK_ID")) define(("ABOUT_IBLOCK_ID"), 7); // ID ИБ О Проекте
if (!defined("SERVICES_IBLOCK_ID")) define(("SERVICES_IBLOCK_ID"), 8); // ID ИБ Услуги
if (!defined("SOCIALS_IBLOCK_ID")) define(("SOCIALS_IBLOCK_ID"), 9); // ID ИБ Соцсети
if (!defined("CATEGORIES_IBLOCK_ID")) define(("CATEGORIES_IBLOCK_ID"), 19); // ID ИБ Категории номеров
if (!defined("TARIFFS_IBLOCK_ID")) define(("TARIFFS_IBLOCK_ID"), 21); // ID ИБ Тарифы номеров
if (!defined("OCCUPANCIES_IBLOCK_ID")) define(("OCCUPANCIES_IBLOCK_ID"), 20); // ID Размещения номеров
if (!defined("AGES_IBLOCK_ID")) define(("AGES_IBLOCK_ID"), 18); // ID Возраст детей
if (!defined("METATAGS_IBLOCK_ID")) define(("METATAGS_IBLOCK_ID"), 15); // ID ИБ Мета-теги
if (!defined("TABS_IBLOCK_ID")) define(("TABS_IBLOCK_ID"), 16); // ID ИБ Табы на главной
if (!defined("CHILDREN_HL_ID")) define(("CHILDREN_HL_ID"), 14); // ID HL Возраст
if (!defined("HTTP_HOST")) define(("HTTP_HOST"), 'https://naturalist.travel'); // Хост
if (!defined("IS_ORDER_TEST_PROP_ID")) define(("IS_ORDER_TEST_PROP_ID"), 19); // Тестовый ли заказ
if (!defined("CERT_VALUE_PROP_ID")) define(("CERT_VALUE_PROP_ID"), 30); // Свойство заказа для указания суммы использованного сетрификата
if (!defined("SMI_IBLOCK_ID")) define(("SMI_IBLOCK_ID"), 27); // ID ИБ Сми о нас
if (!defined("MAIN_SLIDER_IBLOCK_ID")) define(("MAIN_SLIDER_IBLOCK_ID"), 28); // ID ИБ Слайдер на главной
if (!defined("CATALOG_BRONEVIK_OFFERS_IBLOCK_ID")) define(("CATALOG_BRONEVIK_OFFERS_IBLOCK_ID"), 32); // ID ИБ предложений бронивека
if (!defined("CATALOG_IBLOCK_SECTION_UF_EXTERNAL_SERVICE_ID")) define(("CATALOG_IBLOCK_SECTION_UF_EXTERNAL_SERVICE_ID"), 6); // ID ИБ предложений бронивека
if (!defined("CATALOG_IBLOCK_ELEMENT_EXTERNAL_SERVICE_ID")) define(("CATALOG_IBLOCK_ELEMENT_EXTERNAL_SERVICE_ID"), 24); // ID ИБ предложений бронивека
if(!defined("HEADER_SLIDER_IBLOCK_ID")) define(("HEADER_SLIDER_IBLOCK_ID"), 35);

if (!defined("YANDEX_SPLIT_PAYSYSTEM_ID")) define(("YANDEX_SPLIT_PAYSYSTEM_ID"), 6); // ID системы оплаты яндекс сплит

if (!defined("URL_LOGO")) define(("URL_LOGO"), $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_HOST'] . '/logotype.jpg'); // Путь до логотипа для разметок

if (!defined("SBR_LOGIN")) define(("SBR_LOGIN"), "p9727002303-api"); // Login
if (!defined("SBR_PASSWORD")) define(("SBR_PASSWORD"), "RkLw8}vs"); // Password

if (!defined("CERT_DELIVERY_PARENT_ID")) define(("CERT_DELIVERY_PARENT_ID"), 2); // Родительска доставка для сертификатов

define('C_REST_WEB_HOOK_URL', 'https://naturalist.bitrix24.ru/rest/3788/5w9swq0spo5flmyn/'); //url on creat Webhook

// Указать работы скрипта обновления таблицы цен Бново
$GLOBALS['BNOVO_FILES_WORKING'] = false;

// Различные параметры для каталога
const HL_ROOM_FEATURES_ENTITY = 'RoomFeatures';
const CATALOG_ROOM_FEATURES_PROP_CODE = 22;

// Свойства заказа
const ORDER_PROP_PHONE = 1;
const ORDER_PROP_EMAIL = 2;
const ORDER_PROP_NAME = 10;
const ORDER_PROP_LAST_NAME = 11;
const ORDER_PROP_IS_CERT = 21;
const ORDER_PROP_FIZ_VARIANT = 22;
const ORDER_PROP_DOBRO_CERT = 44;
const ORDER_PROP_FIZ_POCKET = 23;
const ORDER_PROP_CITY = 24;
const ORDER_PROP_GIFT_NAME = 25;
const ORDER_PROP_GIFT_EMAIL = 26;
const ORDER_PROP_ELECTRO_VARIANT = 27;
const ORDER_PROP_CONGRATS = 28;
const ORDER_PROP_CERT_PRICE = 29;

const ORDER_PROP_ROOM_PHOTO = 31;
const ORDER_PROP_CHECKIN_TIME = 32;
const ORDER_PROP_CHECOUT_TIME = 33;
const ORDER_PROP_GUESTS_PLACE = 34;
const ORDER_PROP_OBJECT_ADDRESS = 35;
const ORDER_PROP_GUESTS_LINE_UP = 36;
const ORDER_PROP_DATES_NIGHTS = 37;
const ORDER_PROP_CERT_FORMAT = 38;
const ORDER_PROP_CERT_ADDRESS = 39;
const ORDER_PROP_CERT_FILE = 40;
const ORDER_PROP_ENTERED_COUPON = 41;
const ORDER_PROP_BRONEVIK_OFFER_ID = 43;


// Оплаты для сертификатов
const CERT_YANDEX_PAYSYSTEM_ID = 7;
const CERT_YANDEX_SPLIT_PAYSYSTEM_ID = 8;
const CERT_SBER_PAYSYSTEM_ID = 9;
const CERT_CASH_PAYSYSTEM_ID = 10;
const CERT_YOOKASSA_PAYSYSTEM_ID = 12;

// Доставки для сертификатов
const CERT_SELF_DELIVERY_ID = 3;

const CERT_REVIEWS_IBLOCK_ID = 26;

// Фильтры и ЧПУ
const TITLE_PATTERN = ' с ценами: адреса, отзывы | Натуралист';
const DESCRIPTION_START_PATTERN = 'Забронируйте ';
const DESCRIPTION_END_PATTERN = '. В каталоге Натуралист есть цены 2025, отзывы, фото, полный список услуг, координаты на карте.';

const PODBOR_H1_PATTERTN = 'Отдых на природе ';
const PODBOR_TITLE_PATTERTN = ': цены, рейтинг, отзывы | Натуралист';
const PODBOR_DESCRIPTION_PATTERTN = '. Аренда домиков по лучшей цене с быстрым бронированием.';

const FILTER_HL_ENTITY = 'ChpyLinks';
const TYPES_HL_ENTITY = 'CampingTypes';
const REGIONS_HL_ENTITY = 'Regions';
const SUIT_TYPES_HL_ENTITY = 'SuitTypes';
const WATER_HL_ENTITY = 'Water';
const COMMON_WATER_HL_ENTITY = 'WaterCommon';
const REST_VARS_HL_ENTITY = 'RestVariants';
const OBJECT_COMFORT_HL_ENTITY = 'ObjectComfort';
const FOOD_HL_ENTITY = 'CampingFood';
const FEATURES_HL_ENTITY = 'CampingFeatures';
const SITEMAP_LINKS_HL_ENTITY = 'SitemapLinks';
const DIFFERENT_FILTERS_HL_ENTITY = 'DIffFilters';
const ROOM_FEATURES_HL_ENTITY = 'RoomFeatures';

const TRAVELINE_CHECKSUMM_HL_ENTITY = 'CheckSumms';

const BRONEVIK_RATE_TYPE_HL_ENTITY = 'Ratetypebronevik';

const BNOVO_FILES_HL_ENTITY = 'PriceFiles';

const OPEN_CERT_ELEMENT_ID = 15974;

const DEBUG_TELEGRAM_BOT_TOKEN = '7584944033:AAEUAXdKj_3xxc71rirrAwHeD_yGxpUgKvE';
const TELEGRAM_USERS_HL_ENTITY = 'TelegramUsers';

// Группа модераторов
const MODERATOR_USER_GROUP = 14;