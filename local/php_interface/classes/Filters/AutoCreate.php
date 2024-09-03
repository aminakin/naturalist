<?php

namespace Naturalist\Filters;

use Bitrix\Iblock\Elements\ElementServicesTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use \Cutil;

Loader::includeModule('highloadblock');

/**
 * Автосоздание ссылок
 */
class AutoCreate
{
    private const FILTER_ENTITYS = [
        [
            'ENTITY' => FOOD_HL_ENTITY,
            'FILTER' => 'food'
        ],
        [
            'ENTITY' => SUIT_TYPES_HL_ENTITY,
            'FILTER' => 'housetypes'
        ],
        [
            'ENTITY' => OBJECT_COMFORT_HL_ENTITY,
            'FILTER' => 'objectcomforts'
        ],
        [
            'ENTITY' => FEATURES_HL_ENTITY,
            'FILTER' => 'features'
        ],
        [
            'ENTITY' => REST_VARS_HL_ENTITY,
            'FILTER' => 'restvariants'
        ],
    ];

    /**
     * Тип размещения
     */
    public static function createAccomodationTypesLinks()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(TYPES_HL_ENTITY)->getDataClass();

        $query = $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_SKLON')
            ?->fetchAll();

        foreach ($query as $value) {
            if ($value['UF_SKLON'] != '') {
                $links[] = [
                    'UF_NEW_URL' => '/catalog/' . self::getNewUrl($value['UF_SKLON']),
                    'UF_REAL_URL' => '/catalog/?types=' . $value['ID'],
                    'UF_ACTIVE' => 1,
                    'UF_H1' => $value['UF_SKLON'] . ' России',
                    'UF_TITLE' => $value['UF_SKLON'] . TITLE_PATTERN,
                    'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($value['UF_SKLON']) . DESCRIPTION_END_PATTERN,
                    'UF_FILTER_ID' => TYPES_HL_ENTITY . '_' . $value['ID'],
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Регионы
     */
    public static function createRegionsLinks()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(REGIONS_HL_ENTITY)->getDataClass();

        $query =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ->whereNotNull('UF_SKLON')
            ?->fetchAll();

        foreach ($query as $value) {
            if ($value['UF_SKLON'] != '') {
                $links[] = [
                    'UF_NEW_URL' => '/catalog/' . self::getNewUrl($value['UF_SKLON']),
                    'UF_REAL_URL' => '/catalog/?name={"type":"area","item":"' . $value['UF_NAME'] . '","title":"' . $value['UF_NAME'] . '","footnote":""}',
                    'UF_ACTIVE' => 1,
                    'UF_H1' => PODBOR_H1_PATTERTN . $value['UF_SKLON'],
                    'UF_TITLE' => PODBOR_H1_PATTERTN . $value['UF_SKLON'] . PODBOR_TITLE_PATTERTN,
                    'UF_DESCRIPTION' => PODBOR_H1_PATTERTN . $value['UF_SKLON'] . PODBOR_DESCRIPTION_PATTERTN,
                    'UF_FILTER_ID' => REGIONS_HL_ENTITY . '_' . $value['ID'],
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Типы домов
     */
    public static function createHouseTypesLinks()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(SUIT_TYPES_HL_ENTITY)->getDataClass();

        $query =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ?->fetchAll();

        foreach ($query as $value) {
            $links[] = [
                'UF_NEW_URL' => '/catalog/tip-' . self::getNewUrl($value['UF_NAME']),
                'UF_REAL_URL' => '/catalog/?housetypes=' . $value['ID'],
                'UF_ACTIVE' => 1,
                'UF_H1' => PODBOR_H1_PATTERTN . 'в домах типа ' . $value['UF_NAME'],
                'UF_TITLE' => PODBOR_H1_PATTERTN . 'в домах типа ' . $value['UF_NAME'] . PODBOR_TITLE_PATTERTN,
                'UF_DESCRIPTION' => PODBOR_H1_PATTERTN . 'в домах типа ' . $value['UF_NAME'] . PODBOR_DESCRIPTION_PATTERTN,
                'UF_FILTER_ID' => SUIT_TYPES_HL_ENTITY . '_' . $value['ID'],
            ];
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Водоёмы
     */
    public static function createWaterLinks()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();

        $query =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ?->fetchAll();

        foreach ($query as $value) {
            if ($value['UF_SKLON'] != '') {
                $links[] = [
                    'UF_NEW_URL' => '/catalog/' . self::getNewUrl($value['UF_SKLON']),
                    'UF_REAL_URL' => '/catalog/?water=' . $value['ID'],
                    'UF_ACTIVE' => 1,
                    'UF_H1' => PODBOR_H1_PATTERTN . $value['UF_SKLON'],
                    'UF_TITLE' => PODBOR_H1_PATTERTN . $value['UF_SKLON'] . PODBOR_TITLE_PATTERTN,
                    'UF_DESCRIPTION' => PODBOR_H1_PATTERTN . $value['UF_SKLON'] . PODBOR_DESCRIPTION_PATTERTN,
                    'UF_FILTER_ID' => WATER_HL_ENTITY . '_' . $value['ID'],
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Общие водоёмы
     */
    public static function createCommonWaterLinks()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(COMMON_WATER_HL_ENTITY)->getDataClass();

        $query =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ?->fetchAll();

        foreach ($query as $value) {
            if ($value['UF_SKLON'] != '') {
                $links[] = [
                    'UF_NEW_URL' => '/catalog/' . self::getNewUrl($value['UF_SKLON']),
                    'UF_REAL_URL' => '/catalog/?commonwater=' . $value['ID'],
                    'UF_ACTIVE' => 1,
                    'UF_H1' => PODBOR_H1_PATTERTN . $value['UF_SKLON'],
                    'UF_TITLE' => PODBOR_H1_PATTERTN . $value['UF_SKLON'] . PODBOR_TITLE_PATTERTN,
                    'UF_DESCRIPTION' => PODBOR_H1_PATTERTN . $value['UF_SKLON'] . PODBOR_DESCRIPTION_PATTERTN,
                    'UF_FILTER_ID' => COMMON_WATER_HL_ENTITY . '_' . $value['ID'],
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Тип размещения + параметр фильтрации
     */
    public static function createAccomodationAndFilterLinks()
    {
        $first = self::getFilterArray(TYPES_HL_ENTITY);
        $second = self::getAllFiltersArray();

        foreach ($first as $firstElement) {
            foreach ($second as $secondKey => $secondArray) {
                foreach ($secondArray as $secondElement) {
                    $h1 = $firstElement['UF_SKLON'] . ' ' . $secondElement['UF_SKLON'];
                    $links[] = [
                        'UF_NEW_URL' => '/catalog/' . self::getNewUrl($firstElement['UF_SKLON']) . self::getNewUrl($secondElement['UF_SKLON']),
                        'UF_REAL_URL' => '/catalog/?types=' . $firstElement['ID'] . '&' . $secondKey . '=' . $secondElement['ID'],
                        'UF_ACTIVE' => 1,
                        'UF_H1' => $h1,
                        'UF_TITLE' => $h1 . TITLE_PATTERN,
                        'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($h1) . DESCRIPTION_END_PATTERN,
                    ];
                }
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Тип размещения + регион
     */
    public static function createAccomodationAndRegionLinks()
    {
        $first = self::getFilterArray(TYPES_HL_ENTITY);
        $chpyDataClass = HighloadBlockTable::compileEntity(REGIONS_HL_ENTITY)->getDataClass();
        $second =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ->whereNotNull('UF_SKLON')
            ?->fetchAll();

        foreach ($first as $firstElement) {
            foreach ($second as $secondElement) {
                $h1 = $firstElement['UF_SKLON'] . ' ' . $secondElement['UF_SKLON'];
                $links[] = [
                    'UF_NEW_URL' => '/catalog/' . self::getNewUrl($firstElement['UF_SKLON']) . self::getNewUrl($secondElement['UF_SKLON']),
                    'UF_REAL_URL' => '/catalog/?types=' . $firstElement['ID'] . '&name={"type":"area","item":"' . $secondElement['UF_NAME'] . '","title":"' . $secondElement['UF_NAME'] . '","footnote":""}',
                    'UF_ACTIVE' => 1,
                    'UF_H1' => $h1,
                    'UF_TITLE' => $h1 . TITLE_PATTERN,
                    'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($h1) . DESCRIPTION_END_PATTERN,
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Тип размещения + регион + параметр фильтрации
     */
    public static function createAccomodationAndRegionAndFilterLinks()
    {
        $first = self::getFilterArray(TYPES_HL_ENTITY);
        $chpyDataClass = HighloadBlockTable::compileEntity(REGIONS_HL_ENTITY)->getDataClass();
        $second =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ->whereNotNull('UF_SKLON')
            ?->fetchAll();

        $third = self::getAllFiltersArray();

        foreach ($first as $firstElement) {
            foreach ($second as $secondElement) {
                foreach ($third as $thirdKey => $thirdArray) {
                    foreach ($thirdArray as $thirdElement) {
                        $h1 = $firstElement['UF_SKLON'] . ' ' . $secondElement['UF_SKLON'] . ' ' . $thirdElement['UF_SKLON'];
                        $links[] = [
                            'UF_NEW_URL' => '/catalog/' . self::getNewUrl($firstElement['UF_SKLON']) . self::getNewUrl($secondElement['UF_SKLON']) . self::getNewUrl($thirdElement['UF_SKLON']),
                            'UF_REAL_URL' => '/catalog/?types=' . $firstElement['ID'] . '&name={"type":"area","item":"' . $secondElement['UF_NAME'] . '","title":"' . $secondElement['UF_NAME'] . '","footnote":""}&' . $thirdKey . '=' . $thirdElement['ID'],
                            'UF_ACTIVE' => 1,
                            'UF_H1' => $h1,
                            'UF_TITLE' => $h1 . TITLE_PATTERN,
                            'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($h1) . DESCRIPTION_END_PATTERN,
                        ];
                    }
                }
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Тип размещения + водоём
     */
    public static function createAccomodationAndWaterLinks()
    {
        $first = self::getFilterArray(TYPES_HL_ENTITY);
        $chpyDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();
        $second =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ?->fetchAll();

        foreach ($first as $firstElement) {
            foreach ($second as $secondElement) {
                $h1 = $firstElement['UF_SKLON'] . ' ' . $secondElement['UF_SKLON'];
                $links[] = [
                    'UF_NEW_URL' => '/catalog/' . self::getNewUrl($firstElement['UF_SKLON']) . self::getNewUrl($secondElement['UF_SKLON']),
                    'UF_REAL_URL' => '/catalog/?types=' . $firstElement['ID'] . '&water=' . $secondElement['ID'],
                    'UF_ACTIVE' => 1,
                    'UF_H1' => $h1,
                    'UF_TITLE' => $h1 . TITLE_PATTERN,
                    'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($h1) . DESCRIPTION_END_PATTERN,
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Тип размещения + водоём + параметр фильтрации
     */
    public static function createAccomodationAndWaterAndFilterLinks()
    {
        $first = self::getFilterArray(TYPES_HL_ENTITY);
        $chpyDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();
        $second =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ?->fetchAll();

        $third = self::getAllFiltersArray();

        foreach ($first as $firstElement) {
            foreach ($second as $secondElement) {
                foreach ($third as $thirdKey => $thirdArray) {
                    foreach ($thirdArray as $thirdElement) {
                        $h1 = $firstElement['UF_SKLON'] . ' ' . $secondElement['UF_SKLON'] . ' ' . $thirdElement['UF_SKLON'];
                        $links[] = [
                            'UF_NEW_URL' => '/catalog/' . self::getNewUrl($firstElement['UF_SKLON']) . self::getNewUrl($secondElement['UF_SKLON']) . self::getNewUrl($thirdElement['UF_SKLON']),
                            'UF_REAL_URL' => '/catalog/?types=' . $firstElement['ID'] . '&water=' . $secondElement['ID'] . '&' . $thirdKey . '=' . $thirdElement['ID'],
                            'UF_ACTIVE' => 1,
                            'UF_H1' => $h1,
                            'UF_TITLE' => $h1 . TITLE_PATTERN,
                            'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($h1) . DESCRIPTION_END_PATTERN,
                        ];
                    }
                }
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Водоём + параметр фильтрации
     */
    public static function createWaterAndFilterLinks()
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(WATER_HL_ENTITY)->getDataClass();
        $first =  $chpyDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SKLON')
            ?->fetchAll();

        $second = self::getAllFiltersArray();

        foreach ($first as $firstElement) {
            foreach ($second as $secondKey => $secondArray) {
                foreach ($secondArray as $secondElement) {
                    $h1 = $firstElement['UF_SKLON'] . ' ' . $secondElement['UF_SKLON'];
                    $links[] = [
                        'UF_NEW_URL' => '/catalog/' . self::getNewUrl($firstElement['UF_SKLON']) . self::getNewUrl($secondElement['UF_SKLON']),
                        'UF_REAL_URL' => '/catalog/?water=' . $firstElement['ID'] . '&' . $secondKey . '=' . $secondElement['ID'],
                        'UF_ACTIVE' => 1,
                        'UF_H1' => $h1,
                        'UF_TITLE' => $h1 . TITLE_PATTERN,
                        'UF_DESCRIPTION' => DESCRIPTION_START_PATTERN . mb_strtolower($h1) . DESCRIPTION_END_PATTERN,
                    ];
                }
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    private static function getFilterArray($entity)
    {
        $entity = HighloadBlockTable::compileEntity($entity)->getDataClass();

        return $entity::query()
            ->addSelect('*')
            ->where('UF_SHOW_FILTER', 1)
            ?->fetchAll();
    }

    private static function getAllFiltersArray()
    {
        $result = [];
        foreach (self::FILTER_ENTITYS as $entity) {
            $entityDataClass = HighloadBlockTable::compileEntity($entity['ENTITY'])->getDataClass();
            $query = $entityDataClass::query()
                ->addSelect('ID')
                ->addSelect('UF_NAME')
                ->addSelect('UF_SKLON')
                ->where('UF_SHOW_FILTER', 1)
                ?->fetchAll();
            $result[$entity['FILTER']] = $query;
        }

        // Окружение
        $elements = ElementServicesTable::getList([
            'order' => ['SORT' => 'ASC'],
            'select' => ['NAME', 'ID', 'PREVIEW_TEXT'],
            'filter' => ['=ACTIVE' => 'Y', "SHOW_FILTER.VALUE" => 11],
        ])->fetchAll();

        if (!empty($elements)) {
            foreach ($elements as $element) {
                $result['services'][] = [
                    'ID' => $element['ID'],
                    'UF_NAME' => $element['NAME'],
                    'UF_SKLON' => $element['PREVIEW_TEXT'],
                ];
            }
        }

        return $result;
    }

    private static function getNewUrl($path)
    {
        return Cutil::translit($path, 'ru', ['replace_space' => '-', 'replace_other' => '-']) . '/';
    }

    private static function addUrls($urls)
    {
        //self::clearUrlsHl();
        $entity =  HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        $query =  $entity::query()
            ->addSelect('ID')
            ->addSelect('UF_NEW_URL')
            ?->fetchAll();

        foreach ($query as $link) {
            $result[$link['UF_NEW_URL']] = $link;
        }

        foreach ($urls as $url) {
            if (isset($result[$url['UF_NEW_URL']]) && $result[$url['UF_NEW_URL']]['UF_NEW_URL'] == $url['UF_NEW_URL']) {
                $entity::update($result[$url['UF_NEW_URL']]['ID'], $url);
            } else {
                $entity::add($url);
            }
        }
    }

    public static function clearUrlsHl()
    {
        $entity =  HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        $query =  $entity::query()
            ->addSelect('ID')
            ?->fetchAll();

        foreach ($query as $item) {
            $entity::delete($item['ID']);
        }
    }

    /**
     * Запускает все функции создания ЧПУ
     */
    public static function createAllChpys()
    {
        self::createAccomodationTypesLinks();
        self::createCommonWaterLinks();
        self::createHouseTypesLinks();
        self::createRegionsLinks();
        self::createWaterLinks();
        self::createAccomodationAndFilterLinks();
        self::createAccomodationAndRegionLinks();
        self::createAccomodationAndRegionAndFilterLinks();
        self::createAccomodationAndWaterLinks();
        self::createAccomodationAndWaterAndFilterLinks();
    }
}
