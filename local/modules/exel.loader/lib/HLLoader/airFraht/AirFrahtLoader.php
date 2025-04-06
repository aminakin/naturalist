<?php

namespace Calculator\Kploader\HLLoader\airFraht;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class AirFrahtLoader extends HLLoaderAbstract
{
    protected static string $hlAirFrahtName = 'AirFraht';
    protected static mixed $hlAirFrahtEntity;


    /**
     * example
     * тут поля городов
     *   0 => 'ID',
     *    1 => 'Откуда',
     *   2 => 'Куда',
     *   3 => 'Масса мин',
     *   4 => 'Масса мах',
     *   5 => 'Ставка за кг (CNY)',
     *
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_CITY_FROM' => 1,
        'UF_CITY_TO' => 2,
        'UF_WEIGHT_MIN' => 3,
        'UF_WEIGHT_MAX' => 4,
        'UF_RATE_KG_CNY' => 5,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlAirFrahtEntity = self::loadHL(self::$hlAirFrahtName);

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
                $messageRow[] = Loc::getMessage('EXEL_LOADER_SUCCESS_MESSAGE') . ' -> ' . implode(' | ', $row);
            }else{
                $messageRow[] = Loc::getMessage('EXEL_LOADER_ERROR_MESSAGE') . ' -> ' . implode(' | ', $row);
            }
        }

        return self::buildResponce($rowCount, $resultCount, $messageRow);
    }

    /**
     * Поиск и загрзука данных по основной таблице Авиа Фрахт
     *
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow($cityDepatureId, $cityArrivalId, $data)
    {
        $issetRow = self::$hlAirFrahtEntity::query()
            ->addSelect('ID')
            ->where('UF_CITY_FROM', $cityDepatureId)
            ->where('UF_CITY_TO', $cityArrivalId)
            ->where('UF_WEIGHT_MIN', $data[self::$fields['UF_WEIGHT_MIN']])
            ->where('UF_WEIGHT_MAX', $data[self::$fields['UF_WEIGHT_MAX']])
            ->fetch();


        if ($issetRow) {
            self::$hlAirFrahtEntity::update($issetRow['ID'], [
                'UF_RATE_KG_CNY' => $data[self::$fields['UF_RATE_KG_CNY']],
            ]);

            return $issetRow['ID'];
        }

        $newRow = self::$hlAirFrahtEntity::add([
            'UF_CITY_FROM' => $cityDepatureId,
            'UF_CITY_TO' => $cityArrivalId,
            'UF_WEIGHT_MIN' => $data[self::$fields['UF_WEIGHT_MIN']],
            'UF_WEIGHT_MAX' => $data[self::$fields['UF_WEIGHT_MAX']],
            'UF_RATE_KG_CNY' => $data[self::$fields['UF_RATE_KG_CNY']],
        ]);

        return $newRow->getId() ?? false;
    }
}
