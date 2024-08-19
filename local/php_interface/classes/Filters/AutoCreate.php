<?php

namespace Naturalist\Filters;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use \Cutil;

Loader::includeModule('highloadblock');

/**
 * Автосоздание ссылок
 */
class AutoCreate
{
    /**
     * Создание ссылок Тип размещения
     */
    public static function createAccomosationTypesLinks()
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
     * Создание ссылок Регионы
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
                    'UF_H1' => 'Отдых на природе ' . $value['UF_SKLON'],
                    'UF_TITLE' => 'Отдых на природе ' . $value['UF_SKLON'] . ': цены, рейтинг, отзывы | Натуралист',
                    'UF_DESCRIPTION' => 'Отдых на природе ' . $value['UF_SKLON'] . '. Аренда домиков по лучшей цене с быстрым бронированием.',
                    'UF_FILTER_ID' => REGIONS_HL_ENTITY . '_' . $value['ID'],
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Создание ссылок Типы домов
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
                'UF_NEW_URL' => '/catalog/' . self::getNewUrl($value['UF_NAME']),
                'UF_REAL_URL' => '/catalog/?housetypes=' . $value['ID'],
                'UF_ACTIVE' => 1,
                'UF_H1' => 'Отдых на природе в домах типа ' . $value['UF_NAME'],
                'UF_TITLE' => 'Отдых на природе в домах типа ' . $value['UF_NAME'] . ': цены, рейтинг, отзывы | Натуралист',
                'UF_DESCRIPTION' => 'Отдых на природе в домах типа ' . $value['UF_NAME'] . '. Аренда домиков по лучшей цене с быстрым бронированием.',
                'UF_FILTER_ID' => SUIT_TYPES_HL_ENTITY . '_' . $value['ID'],
            ];
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    /**
     * Создание ссылок Водоёмы
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
                    'UF_H1' => 'Отдых на природе ' . $value['UF_SKLON'],
                    'UF_TITLE' => 'Отдых на природе ' . $value['UF_SKLON'] . ': цены, рейтинг, отзывы | Натуралист',
                    'UF_DESCRIPTION' => 'Отдых на природе ' . $value['UF_SKLON'] . '. Аренда домиков по лучшей цене с быстрым бронированием.',
                    'UF_FILTER_ID' => WATER_HL_ENTITY . '_' . $value['ID'],
                ];
            }
        }

        if (isset($links) && is_array($links)) {
            self::addUrls($links);
        }
    }

    private static function getNewUrl($path)
    {
        return Cutil::translit($path, 'ru', ['replace_space' => '-', 'replace_other' => '-']) . '/';
    }

    private static function addUrls($urls)
    {
        //self::clearUrlsHl();
        $entity =  HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        foreach ($urls as $url) {
            $entity::add($url);
        }
    }

    private static function clearUrlsHl()
    {
        $entity =  HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        $query =  $entity::query()
            ->addSelect('ID')
            ?->fetchAll();

        foreach ($query as $item) {
            $entity::delete($item['ID']);
        }
    }
}
