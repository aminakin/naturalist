<?php

namespace Calculator\Kploader\HLLoader\autoRoutes;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class AutoRoutesLoader extends HLLoaderAbstract
{
    protected static string $hlAutoRoutesName = 'AutoRoutes';
    protected static mixed $hlAutoRoutesEntity;


    /**
     * example
     *   0 => 'ID',
     *   1 => 'Откуда Страна (string)',
     *   2 => 'Откуда Город (связь)',
     *   3 => 'Куда Страна (string)',
     *   4 => 'Куда Город (связь)',
     *   5 => 'Ставка до ТС (USD)',
     *   6 => 'Ставка после ТС (USD)',
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_STATE_FROM' => 1,
        'UF_FROM' => 2,
        'UF_STATE_TO' => 3,
        'UF_TO' => 4,
        'UF_TO_TS' => 5,
        'UF_AFTER_TS' => 6,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlAutoRoutesEntity = self::loadHL(self::$hlAutoRoutesName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $cityDepatureId = self::findCityDepatureByName($row[self::$fields['UF_STATE_FROM']], $row[self::$fields['UF_FROM']]);
            $cityArrivalId = self::findCityArrivalByName($row[self::$fields['UF_STATE_TO']], $row[self::$fields['UF_TO']]);

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
     * Поиск и загрузка данных по основной таблице Маршруты Авто
     *
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow($cityDepatureId, $cityArrivalId, $data)
    {
        $issetRow = self::$hlAutoRoutesEntity::query()
            ->addSelect('ID')
            ->where('UF_FROM', $cityDepatureId)
            ->where('UF_TO', $cityArrivalId)
            ->fetch();


        if ($issetRow) {
            self::$hlAutoRoutesEntity::update($issetRow['ID'], [
                'UF_TO_TS' => $data[self::$fields['UF_TO_TS']],
                'UF_AFTER_TS' => $data[self::$fields['UF_AFTER_TS']],
            ]);

            return $issetRow['ID'];
        }

        $newRow = self::$hlAutoRoutesEntity::add([
            'UF_STATE_FROM' => $data[self::$fields['UF_STATE_FROM']],
            'UF_FROM' => $cityDepatureId,
            'UF_STATE_TO' => $data[self::$fields['UF_STATE_TO']],
            'UF_TO' => $cityArrivalId,
            'UF_TO_TS' => $data[self::$fields['UF_TO_TS']],
            'UF_AFTER_TS' => $data[self::$fields['UF_AFTER_TS']],
        ]);

        return $newRow->getId() ?? false;
    }
}
