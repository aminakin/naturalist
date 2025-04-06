<?php

namespace Calculator\Kploader\HLLoader\rzd40;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class RZD40Loader extends HLLoaderAbstract
{
    protected static string $hlRcContainerName = 'RrContainer';
    protected static mixed $hlRcContainerEntity;


    /**
     * example
     * тут поля городов
     *
     *   0 => 'ID',
     *   1 => 'Откуда',
     *   2 => 'Куда',
     *   3 => 'Ставка ЖД, USD',
     *   4 => 'Обработка на станции, RUB',
     *   5 => 'Доставка по городу, RUB',
     *     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_CITY_FROM' => 1,
        'UF_CITY_TO' => 2,
        'UF_RR_TARIF' => 3,
        'UF_STATION_PROC' => 4,
        'UF_DELIVERY' => 5,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlRcContainerEntity = self::loadHL(self::$hlRcContainerName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $cityDepatureId = self::findCityDepatureByName('', $row[self::$fields['UF_CITY_FROM']]);
            $cityArrivalId = self::findCityArrivalByName('', $row[self::$fields['UF_CITY_TO']]);

            if ($cityDepatureId && $cityArrivalId) {
                $result = self::findRow($cityDepatureId, $cityArrivalId, $row);
            }

            if ($result) {
                $resultCount++;
                $messageRow[] = Loc::getMessage('EXEL_LOADER_SUCCESS_MESSAGE') .' -> ' . implode(' | ', $row);
            }else{
                $messageRow[] = Loc::getMessage('EXEL_LOADER_ERROR_MESSAGE') .' -> ' . implode(' | ', $row);
            }
        }

        return self::buildResponce($rowCount, $resultCount, $messageRow);
    }

    /**
     * Поиск и загрзука данных по основной таблице ЖД Контейнер 40 фут
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow($cityDepatureId, $cityArrivalId, $data)
    {
        $issetRow = self::$hlRcContainerEntity::query()
            ->addSelect('ID')
            ->where('UF_FROM', $cityDepatureId)
            ->where('UF_TO', $cityArrivalId)
            ->fetch();


        if ($issetRow) {
            self::$hlRcContainerEntity::update($issetRow['ID'], [
                'UF_RR_TARIF' => $data[self::$fields['UF_RR_TARIF']],
                'UF_STATION_PROC' => $data[self::$fields['UF_STATION_PROC']],
                'UF_DELIVERY' => $data[self::$fields['UF_DELIVERY']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlRcContainerEntity::add([
            'UF_FROM' => $cityDepatureId,
            'UF_TO' => $cityArrivalId,
            'UF_RR_TARIF' => $data[self::$fields['UF_RR_TARIF']],
            'UF_STATION_PROC' => $data[self::$fields['UF_STATION_PROC']],
            'UF_DELIVERY' => $data[self::$fields['UF_DELIVERY']],
        ]);

        return $newRow->getId() ?? false;
    }
}
