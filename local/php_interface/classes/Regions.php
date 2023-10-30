<?php

namespace Naturalist;


use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

/**
 * класс для работы с HL регионов и городов
 */
class Regions
{
    protected static $regionsHL = 'Regions';
    protected static $citiesHL = 'Cities';

    /**
     * Поиск города по наименованию
     * @param $cityName
     * @return array
     */
    public static function getCityByName($cityName)
    {
        if (is_array($cityName)) {
            $cityName = $cityName[0];
        }

        $citiesDataClass = HighloadBlockTable::compileEntity(self::$citiesHL)->getDataClass();
        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();

        return $citiesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_REGION')
            ->addSelect('REGION', 'REGION_')
            ->registerRuntimeField('REGION', [
                'data_type' => $regionesDataClass,
                'reference' => [
                    'this.UF_REGION' => 'ref.ID'
                ]
            ])
            ->whereLike('UF_NAME', '%' . $cityName . '%')
            ?->fetchAll() ?? [];
    }

    /**
     * Поиск регионов по городам
     * @param $cityName
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getRegionByCity($cityName)
    {
        if (is_array($cityName)) {
            $cityName = $cityName[0];
        }

        $citiesDataClass = HighloadBlockTable::compileEntity(self::$citiesHL)->getDataClass();
        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();

        $citiesData = $citiesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_REGION')
            ->whereLike('UF_NAME', '%' . $cityName . '%')
            ?->fetchAll() ?? [];

        $arResultRegion = [];
        foreach ($citiesData as $cityData) {
            $arResultRegion[] = $regionesDataClass::query()
                ->addSelect('ID')
                ->addSelect('UF_NAME')
                ->where('ID', $cityData['UF_REGION'])
                ?->fetchAll() ?? [];
        }

        return $arResultRegion;
    }

    /**
     * Поиск региона по name
     * @param $region
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getRegionByName($regionName)
    {
        if (is_array($regionName)) {
            $regionName = $regionName[0];
        }

        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();

        return $regionesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SORT')
            ->addSelect('UF_COORDS')
            ->whereLike('UF_NAME', '%' . $regionName . '%')
            ?->fetchAll() ?? [];
    }

    /**
     * Возвращает список избранных регионов
     *
     * @return array
     */
    public static function getFavoriteRegions()
    {
        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();

        return $regionesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SORT')
            ->addSelect('UF_COORDS')
            ->where('UF_FAVORITE', true)
            ?->fetchAll() ?? [];
    }

    /**
     * Поиск региона по ИД
     * @param $regionID
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getRegionById($regionID)
    {
        if ($regionID == false) {
            return false;
        }

        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();
        $citiesDataClass = HighloadBlockTable::compileEntity(self::$citiesHL)->getDataClass();

        return $regionesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SORT')
            ->addSelect('UF_COORDS')
            ->addSelect('UF_CENTER')
            ->addSelect('UF_CENTER_NAME_RU')
            ->addSelect('CENTER', 'CENTER_')
            ->registerRuntimeField('CENTER', [
                'data_type' => $citiesDataClass,
                'reference' => [
                    'this.UF_CENTER' => 'ref.ID'
                ]
            ])
            ->where('ID', $regionID)
            ?->fetch() ?? [];
    }

    /**
     * Список регионов
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getRegionList($ignoredIds = [])
    {
        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();

        return $regionesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SORT')
            ->addSelect('UF_COORDS')
            ->whereNotIn('ID', $ignoredIds)
            ?->fetchAll() ?? [];
    }

    /**
     * Поиск в базе регионов по наименованию
     * @param $name
     * @return array
     */
    public static function RegionFilterSearcher($searchName)
    {
        $arRegions = self::getRegionByName($searchName);
        $arCityRegions = self::getRegionByCity($searchName);

        $arRegionIds = [];
        if (is_array($arRegions) && !empty($arRegions)) {
            foreach ($arRegions as $arRegion) {
                $arRegionIds[] = $arRegion['ID'];
            }
        }

        if (is_array($arCityRegions) && !empty($arCityRegions)) {
            foreach ($arCityRegions as $arCityRegion) {
                foreach ($arCityRegion as $region) {
                    $arRegionIds[] = $region['ID'];
                }
            }
        }

        return $arRegionIds ?? [];
    }
}