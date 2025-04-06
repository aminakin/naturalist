<?php

namespace Calculator\Kploader\HLLoader\autoSvh;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class AutoSvhLoader extends HLLoaderAbstract
{
    protected static string $hlAutoSvhContainerName = 'AutoSvh';
    protected static mixed $hlAutoSvhContainerEntity;


    /**
     * example
     * тут поля городов
     *
     *   0 => 'ID',
     *   1 => 'Куда',
     *   2 => 'Ставка (руб)',
     *   3 => 'Откуда',
     *     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_TO' => 1,
        'UF_RATE' => 2,
        'UF_STATE_FROM' => 3,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlAutoSvhContainerEntity = self::loadHL(self::$hlAutoSvhContainerName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $cityArrivalId = self::findCityArrivalByName('', $row[self::$fields['UF_TO']]);

            if ($cityArrivalId) {
                $result = self::findRow($cityArrivalId, $row);
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
     * Поиск и загрзука данных по основной таблице Ставки СВХ Авто
     *
     *
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow( $cityArrivalId, $data)
    {
        $issetRow = self::$hlAutoSvhContainerEntity::query()
            ->addSelect('ID')
            ->where('UF_TO', $cityArrivalId)
            ->fetch();


        if ($issetRow) {
            self::$hlAutoSvhContainerEntity::update($issetRow['ID'], [
                'UF_RATE' => $data[self::$fields['UF_RATE']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlAutoSvhContainerEntity::add([
            'UF_TO' => $cityArrivalId,
            'UF_RATE' => $data[self::$fields['UF_RATE']],
            'UF_STATE_FROM' => $data[self::$fields['UF_STATE_FROM']],
        ]);

        return $newRow->getId() ?? false;
    }
}
