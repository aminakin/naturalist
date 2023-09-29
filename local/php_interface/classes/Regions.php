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
     * @return void
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
            ->fetchAll();
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
            ->fetchAll();

        $arResultRegion = [];
        foreach ($citiesData as $cityData) {
            $arResultRegion[] = $regionesDataClass::query()
                ->addSelect('ID')
                ->addSelect('UF_NAME')
                ->where('ID', $cityData['UF_REGION'])
                ->fetch();
        }

        return $arResultRegion;
    }

    /**
     * Поиск региона по name
     * @param $region
     * @return mixed
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
            ->fetchAll();
    }

    /**
     * Поиск региона по ИД
     * @param $regionID
     * @return mixed
     * @throws \Bitrix\Main\SystemException
     */
    public static function getRegionById($regionID)
    {
        if ($regionID == false) {
            return false;
        }

        $regionesDataClass = HighloadBlockTable::compileEntity(self::$regionsHL)->getDataClass();

        return $regionesDataClass::query()
            ->addSelect('ID')
            ->addSelect('UF_NAME')
            ->addSelect('UF_SORT')
            ->addSelect('UF_COORDS')
            ->where('ID', $regionID)
            ->fetch();
    }

    /**
     * Список регионов
     * @return mixed
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
            ->fetchAll();
    }
}