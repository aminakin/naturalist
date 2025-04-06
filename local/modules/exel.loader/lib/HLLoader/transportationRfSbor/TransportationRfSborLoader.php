<?php

namespace Calculator\Kploader\HLLoader\transportationRfSbor;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class TransportationRfSborLoader extends HLLoaderAbstract
{
    protected static string $hlTransportationRfSborContainerName = 'TransportationRfSbor';
    protected static mixed $hlTransportationRfSborContainerEntity;

    /**
     * example
     * тут поля городов
     *
     *   0 => 'ID',
     *   1 => 'Куда',
     *   2 => 'Масса брутто, кг MIN',
     *   3 => 'Масса брутто, кг MAX',
     *   4 => 'Объем, м3 MIN',
     *   5 => 'Объем, м3 MAX',
     *   6 => 'Стоимость, RUB',
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_CITY_TO' => 1,
        'UF_GROSS_MIN' => 2,
        'UF_GROSS_MAX' => 3,
        'UF_VOLUME_MIN' => 4,
        'UF_VOLUME_MAX' => 5,
        'UF_COST_RUB' => 6,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlTransportationRfSborContainerEntity = self::loadHL(self::$hlTransportationRfSborContainerName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {

            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $cityArrivalId = self::findCityArrivalByName('', $row[self::$fields['UF_CITY_TO']]);

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
        $issetRow = self::$hlTransportationRfSborContainerEntity::query()
            ->addSelect('ID')
            ->where('UF_CITY_TO', $cityArrivalId)
            ->where('UF_GROSS_MIN', $data[self::$fields['UF_GROSS_MIN']])
            ->where('UF_GROSS_MAX', $data[self::$fields['UF_GROSS_MAX']])
            ->where('UF_VOLUME_MIN', $data[self::$fields['UF_VOLUME_MIN']])
            ->where('UF_COST_RUB', $data[self::$fields['UF_COST_RUB']])
            ->fetch();

        if ($issetRow) {
            self::$hlTransportationRfSborContainerEntity::update($issetRow['ID'], [
                'UF_GROSS_MIN' => $data[self::$fields['UF_GROSS_MIN']],
                'UF_GROSS_MAX' => $data[self::$fields['UF_GROSS_MAX']],
                'UF_VOLUME_MIN' => $data[self::$fields['UF_VOLUME_MIN']],
                'UF_VOLUME_MAX' => $data[self::$fields['UF_VOLUME_MAX']],
                'UF_COST_RUB' => $data[self::$fields['UF_COST_RUB']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlTransportationRfSborContainerEntity::add([
            'UF_CITY_TO' => $cityArrivalId,
            'UF_GROSS_MIN' => $data[self::$fields['UF_GROSS_MIN']],
            'UF_GROSS_MAX' => $data[self::$fields['UF_GROSS_MAX']],
            'UF_VOLUME_MIN' => $data[self::$fields['UF_VOLUME_MIN']],
            'UF_VOLUME_MAX' => $data[self::$fields['UF_VOLUME_MAX']],
            'UF_COST_RUB' => $data[self::$fields['UF_COST_RUB']],
        ]);

        return $newRow->getId() ?? false;
    }
}
