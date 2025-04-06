<?php

namespace Calculator\Kploader\HLLoader\rzdConstrukt;

use Bitrix\Landing\Debug;
use Bitrix\Main\Localization\Loc;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class RZDConstruktLoader extends HLLoaderAbstract
{

    protected static string $hlRrContainerUniteName = 'RrContainerUnite';
    protected static mixed $hlRrContainerUniteEntity;


    /**
     * example
     * тут поля городов
     * 0 => 'ID',
     *   1 => 'Откуда',
     *   2 => 'Куда',
     *  3 => 'Масса брутто, кг MIN',
    *   4 => 'Масса брутто, кг MAX',
    *   5 => 'Ставка за кг, USD',
    *   6 => 'Минималка USD',
    *   7 => NULL,
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_CITY_FROM' => 1,
        'UF_CITY_TO' => 2,
        'UF_MIN_WEIGHT' => 3,
        'UF_MAX_WEIGHT' => 4,
        'UF_KG_USD' => 5,
        'UF_USD_MIN' => 6,
    ];

    public static function loadData($data)
    {
        self::$hlRrContainerUniteEntity = self::loadHL(self::$hlRrContainerUniteName);

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
     * Поиск и загрзука данных по основной таблице ЖД Контейнер Сборка
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    private static function findRow($cityDepatureId, $cityArrivalId, $data)
    {
        $issetRow = self::$hlRrContainerUniteEntity::query()
            ->addSelect('ID')
            ->where('UF_FROM', $cityDepatureId)
            ->where('UF_TO', $cityArrivalId)
            ->where('UF_MIN_WEIGHT', $data[self::$fields['UF_MIN_WEIGHT']])
            ->where('UF_MAX_WEIGHT', $data[self::$fields['UF_MAX_WEIGHT']])
            ->fetch();


        if ($issetRow) {
            self::$hlRrContainerUniteEntity::update($issetRow['ID'], [
                'UF_KG_USD' => $data[self::$fields['UF_KG_USD']],
                'UF_USD_MIN' => $data[self::$fields['UF_USD_MIN']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlRrContainerUniteEntity::add([
            'UF_FROM' => $cityDepatureId,
            'UF_TO' => $cityArrivalId,
            'UF_MIN_WEIGHT' => $data[self::$fields['UF_MIN_WEIGHT']],
            'UF_MAX_WEIGHT' => $data[self::$fields['UF_MAX_WEIGHT']],
            'UF_KG_USD' => $data[self::$fields['UF_KG_USD']],
            'UF_USD_MIN' => $data[self::$fields['UF_USD_MIN']],
        ]);

        return $newRow->getId() ?? false;
    }
}
