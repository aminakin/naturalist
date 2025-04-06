<?php

namespace Calculator\Kploader\HLLoader\localDeliverySbor;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class LocalDeliverySborLoader extends HLLoaderAbstract
{
    protected static string $hlLocalDeliverySborName = 'LocalDeliverySbor';
    protected static mixed $hlLocalDeliverySborEntity;


    /**
     * example
     * тут поля городов
     *  0 => 'ID',
     *  1 => 'Китай', - Страна (strinf)
     *  2 => 'Нингбо', - откуда Город (связь)
     *  3 => 'Shanghai', - куда Город (связь)
     *  4 => '0,07', - Доставка за кг (USD)
     *  5 => '24', - Доставка на м3 (USD)
     *  5 => '7,5', - Размещение склад за кг (USD)
     *  6 => '0,015', - Размещение склад за м3 (USD)
     *  8 => '0,06', - ПРР склад за кг (USD)
     *  9 => '30', - ПРР склад за м3 (USD)
     *  10 => '22', -Фикс плата документы (USD)
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_STATE_FROM' => 1,
        'UF_CITY_FROM' => 2,
        'UF_CITY_TO' => 3,
        'UF_DELIVERY_KG_USD' => 4,
        'UF_DELIVERY_KG_M3_USD' => 5,
        'UF_PLACE_WARE_KG' => 6,
        'UF_PLACE_WARE_M3' => 7,
        'UF_PPR_WARE_KG' => 8,
        'UF_PPR_WARE_M3' => 9,
        'UF_FIX_COST_DOCUMENT_USD' => 10,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlLocalDeliverySborEntity = self::loadHL(self::$hlLocalDeliverySborName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $cityDepatureId = self::findCityDepatureByName($row[self::$fields['UF_STATE_FROM']], $row[self::$fields['UF_CITY_FROM']]);
            $cityArrivalId = self::findCityLocalToName($row[self::$fields['UF_STATE_FROM']], $row[self::$fields['UF_CITY_TO']]);

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
     * Поиск и загрзука данных по основной таблице ЖД Контейнер 40 фут
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow($cityDepatureId, $cityArrivalId, $data)
    {
        $issetRow = self::$hlLocalDeliverySborEntity::query()
            ->addSelect('ID')
            ->where('UF_CITY_FROM', $cityDepatureId)
            ->where('UF_CITY_TO', $cityArrivalId)
            ->fetch();


        if ($issetRow) {
            self::$hlLocalDeliverySborEntity::update($issetRow['ID'], [
                'UF_STATE_FROM' => $data[self::$fields['UF_STATE_FROM']],
                'UF_DELIVERY_KG_USD' => $data[self::$fields['UF_DELIVERY_KG_USD']],
                'UF_DELIVERY_KG_M3_USD' => $data[self::$fields['UF_DELIVERY_KG_M3_USD']],
                'UF_PLACE_WARE_M3' => $data[self::$fields['UF_PLACE_WARE_M3']],
                'UF_PLACE_WARE_KG' => $data[self::$fields['UF_PLACE_WARE_KG']],
                'UF_PPR_WARE_KG' => $data[self::$fields['UF_PPR_WARE_KG']],
                'UF_PPR_WARE_M3' => $data[self::$fields['UF_PPR_WARE_M3']],
                'UF_FIX_COST_DOCUMENT_USD' => $data[self::$fields['UF_FIX_COST_DOCUMENT_USD']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlLocalDeliverySborEntity::add([
            'UF_STATE_FROM' => $data[self::$fields['UF_STATE_FROM']],
            'UF_CITY_FROM' => $cityDepatureId,
            'UF_CITY_TO' => $cityArrivalId,
            'UF_DELIVERY_KG_USD' => $data[self::$fields['UF_DELIVERY_KG_USD']],
            'UF_DELIVERY_KG_M3_USD' => $data[self::$fields['UF_DELIVERY_KG_M3_USD']],
            'UF_PLACE_WARE_M3' => $data[self::$fields['UF_PLACE_WARE_M3']],
            'UF_PLACE_WARE_KG' => $data[self::$fields['UF_PLACE_WARE_KG']],
            'UF_PPR_WARE_KG' => $data[self::$fields['UF_PPR_WARE_KG']],
            'UF_PPR_WARE_M3' => $data[self::$fields['UF_PPR_WARE_M3']],
            'UF_FIX_COST_DOCUMENT_USD' => $data[self::$fields['UF_FIX_COST_DOCUMENT_USD']],
        ]);

        return $newRow->getId() ?? false;
    }
}
