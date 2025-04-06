<?php

namespace Calculator\Kploader\HLLoader;

use Bitrix\Iblock\ORM\Query;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class HLLoaderAbstract
{
    protected static string $hlCityDepatureName = 'CityFrom';
    protected static mixed $hlCityDepatureEntity;
    protected static string $hlCityArrivalName = 'CityTo';
    protected static mixed $hlCityArrivalEntity;
    protected static string $hlCityLocalName = 'CityToLocal';
    protected static mixed $hlCityLocalEntity;

    /**
     * example
     * 0 => 'Китай',
     * 1 => 'China',
     * 2 => 'Шанхай',
     * 3 => 'Shanghai',
     * 4 => 'Россия',
     * 5 => 'Russia',
     * 6 => 'Москва',
     * 7 => 'Moscow',
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $cityFields = [
        /* куда */
        'UF_DEPATURE_COUNTRY' => 1,
        'UF_DEPATURE_CITY' => 2,
        /* откуда */
        'UF_ARRIVAL_COUNTRY' => 3,
        'UF_ARRIVAL_CITY' => 4,
    ];

    abstract public static function loadData($data);


    /**
     * Поиск по базе откуда в связке город - страна
     * @param $data
     * @return mixed
     */
    protected static function findCityDepatureIdByCityAndCountry($data)
    {
        self::$hlCityDepatureEntity = self::loadHL(self::$hlCityDepatureName);

        $issetCityFrom = self::$hlCityDepatureEntity::query()
            ->addSelect('ID')
            ->where('UF_DEPATURE_COUNTRY', $data[self::$cityFields['UF_DEPATURE_COUNTRY']])
            ->where('UF_DEPATURE_CITY', $data[self::$cityFields['UF_DEPATURE_CITY']])
            ->fetch();

        if ($issetCityFrom) {
            return $issetCityFrom['ID'];
        }

        $newCityFrom = self::$hlCityDepatureEntity::add([
            'UF_DEPATURE_COUNTRY' => $data[self::$cityFields['UF_DEPATURE_COUNTRY']],
            'UF_DEPATURE_CITY' => $data[self::$cityFields['UF_DEPATURE_CITY']],
            'UF_DEPATURE_COUNTRY_ENG' => $data[self::$cityFields['UF_DEPATURE_COUNTRY_ENG']],
            'UF_DEPATURE_CITY_ENG' => $data[self::$cityFields['UF_DEPATURE_CITY_ENG']],
        ]);


        return $newCityFrom->getId() ?? false;
    }

    /**
     * Поиск по базе куда в связке город - страна
     * @param $data
     * @return mixed
     */
    protected static function findCityArrivalIdByCityAndCountry($data)
    {
        self::$hlCityArrivalEntity = self::loadHL(self::$hlCityArrivalName);

        $issetCityTo = self::$hlCityArrivalEntity::query()
            ->addSelect('ID')
            ->where('UF_ARRIVAL_COUNTRY', $data[self::$cityFields['UF_ARRIVAL_COUNTRY']])
            ->where('UF_ARRIVAL_CITY', $data[self::$cityFields['UF_ARRIVAL_CITY']])
            ->fetch();

        if ($issetCityTo) {
            return $issetCityTo['ID'];
        }

        $newCityTo = self::$hlCityArrivalEntity::add([
            'UF_ARRIVAL_COUNTRY' => $data[self::$cityFields['UF_ARRIVAL_COUNTRY']],
            'UF_ARRIVAL_CITY' => $data[self::$cityFields['UF_ARRIVAL_CITY']],
            'UF_ARRIVAL_COUNTRY_ENG' => $data[self::$cityFields['UF_ARRIVAL_COUNTRY_ENG']],
            'UF_ARRIVAL_CITY_ENG' => $data[self::$cityFields['UF_ARRIVAL_CITY_ENG']],
        ]);


        return $newCityTo->getId() ?? false;
    }

    /**
     * Поиск города откуда только по названию
     * @param $cityName
     * @return mixed
     */
    protected static function findCityDepatureByName($countryName, $cityName)
    {
        self::$hlCityDepatureEntity = self::loadHL(self::$hlCityDepatureName);

        $issetCityFrom = self::$hlCityDepatureEntity::query()
            ->addSelect('ID')
            ->where(
                Query::filter()
                    ->logic('or')
                    ->whereLike('UF_DEPATURE_CITY', '%' . $cityName . '%')
                    ->whereLike('UF_DEPATURE_CITY_ENG', '%' . $cityName . '%')
            )
            ->fetch();

        if ($issetCityFrom) {
            return $issetCityFrom['ID'];
        }

        $newCityFrom = self::$hlCityDepatureEntity::add([
            'UF_DEPATURE_COUNTRY' => $countryName,
            'UF_DEPATURE_CITY' => $cityName,
            'UF_DEPATURE_COUNTRY_ENG' => $countryName,
            'UF_DEPATURE_CITY_ENG' => $cityName,
        ]);

        return $newCityFrom->getId() ?? false;
    }

    /**
     * Поиск города куда только по названию
     * @param $cityName
     * @return mixed
     */
    protected static function findCityArrivalByName($countryName, $cityName)
    {
        self::$hlCityArrivalEntity = self::loadHL(self::$hlCityArrivalName);

        $issetCityTo = self::$hlCityArrivalEntity::query()
            ->addSelect('ID')
            ->where(
                Query::filter()
                    ->logic('or')
                    ->whereLike('UF_ARRIVAL_CITY', '%' . $cityName . '%')
                    ->whereLike('UF_ARRIVAL_CITY_ENG', '%' . $cityName . '%')
            )
            ->fetch();

        if ($issetCityTo) {
            return $issetCityTo['ID'];
        }

        $newCityTo = self::$hlCityArrivalEntity::add([
            'UF_ARRIAVAL_COUNTRY' => $countryName,
            'UF_ARRIAVAL_CITY' => $cityName,
            'UF_ARRIAVAL_COUNTRY_ENG' => $countryName,
            'UF_ARRIAVAL_CITY_ENG' => $cityName,
        ]);

        return $newCityTo->getId() ?? false;
    }


    /**
     * Поиск города Пункты назначения для локальной доставки только по названию
     * @param $cityName
     * @return mixed
     */
    protected static function findCityLocalToName($countryName, $cityName)
    {
        self::$hlCityLocalEntity = self::loadHL(self::$hlCityLocalName);

        $issetCityTo = self::$hlCityLocalEntity::query()
            ->addSelect('ID')
            ->where(
                Query::filter()
                    ->logic('or')
                    ->whereLike('UF_ARRIVAL_CITY', '%' . $cityName . '%')
                    ->whereLike('UF_ARRIVAL_CITY_ENG', '%' . $cityName . '%')
            )
            ->fetch();

        if ($issetCityTo) {
            return $issetCityTo['ID'];
        }

        $newCityTo = self::$hlCityLocalEntity::add([
            'UF_ARRIVAL_COUNTRY' => $countryName,
            'UF_ARRIVAL_CITY' => $cityName,
            'UF_ARRIVAL_COUNTRY_ENG' => $countryName,
            'UF_ARRIVAL_CITY_ENG' => $cityName,
        ]);

        return $newCityTo->getId() ?? false;
    }

    /**
     * @param $hlCode
     * @return mixed
     * @throws \Bitrix\Main\SystemException
     */
    protected static function loadHL($hlCode): mixed
    {
        return HLHelper::getHLEntity($hlCode);
    }

    /**
     * Ответ для странички
     * @return string
     */
    protected static function buildResponce($rowCount = 0, $resultCount = 0, $messageRow = []): string
    {
        return Loc::getMessage('EXEL_LOADER_RESPONCE_MESSEGE', [
            '#ROW_COUNT#' => $rowCount,
            '#RESULT_COUNT#' => $resultCount,
            '#MESSAGE_ROW#' => implode('<br>', $messageRow),
        ]);
    }
}
