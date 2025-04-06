<?php

namespace Calculator\Kploader\HLLoader\seaContainer;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class SeaContainerLoader extends HLLoaderAbstract
{
    protected static string $hlSeaContainerName = 'SeaContainer';
    protected static mixed $hlSeaContainerEntity;


    /**
     * example
     * тут поля городов
     *
     *   5 => 'Доставка по морю 20 фут, 20 фут утяж (USD)',
     *   6 => 'Доставка по ЖД 20 фут (RUB)',
     *   7 => 'доставка по ЖД 20 фут утяж (RUB.)',
     *   8 => 'Доставка по морю (USD)',
     *   9 => 'Доставка по ЖД 40 фут (руб.)',
     *   10 => 'Доставка по городу 20 фут, 20 фут утяж в (RUB)',
     *   11 => 'Доставка по городу 40 фут в (RUB)',
     *   12 => 'Удорожание перевес (руб.)',
     *
     *
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_SHIP_SEA_20FT_20FTW_USD' => 5,
        'UF_DELIVERY_RW_20FT_RUB' => 6,
        'UF_DELIVERY_RW_20FTW_RUB' => 7,
        'UF_SHIP_SEA_USD' => 8,
        'UF_DELIVERY_RW_40FT_RUB' => 9,
        'UF_DELIVERY_CITY_20FT_20FTW_RUB' => 10,
        'UF_DELIVERY_CITY_40FT_RUB' => 11,
        'UF_DELIVERY_CITY_OVERLOAD_RUB' => 12,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlSeaContainerEntity = self::loadHL(self::$hlSeaContainerName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {

            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $cityDepatureId = self::findCityDepatureIdByCityAndCountry($row);
            $cityArrivalId = self::findCityArrivalIdByCityAndCountry($row);

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
        $issetRow = self::$hlSeaContainerEntity::query()
            ->addSelect('ID')
            ->where('UF_CITY_FROM', $cityDepatureId)
            ->where('UF_CITY_TO', $cityArrivalId)
            ->fetch();


        if ($issetRow) {
            self::$hlSeaContainerEntity::update($issetRow['ID'], [
                'UF_SHIP_SEA_20FT_20FTW_USD' => $data[self::$fields['UF_SHIP_SEA_20FT_20FTW_USD']],
                'UF_DELIVERY_RW_20FT_RUB' => $data[self::$fields['UF_DELIVERY_RW_20FT_RUB']],
                'UF_DELIVERY_RW_20FTW_RUB' => $data[self::$fields['UF_DELIVERY_RW_20FTW_RUB']],
                'UF_SHIP_SEA_USD' => $data[self::$fields['UF_SHIP_SEA_USD']],
                'UF_DELIVERY_RW_40FT_RUB' => $data[self::$fields['UF_DELIVERY_RW_40FT_RUB']],
                'UF_DELIVERY_CITY_20FT_20FTW_RUB' => $data[self::$fields['UF_DELIVERY_CITY_20FT_20FTW_RUB']],
                'UF_DELIVERY_CITY_40FT_RUB' => $data[self::$fields['UF_DELIVERY_CITY_40FT_RUB']],
                'UF_DELIVERY_CITY_OVERLOAD_RUB' => $data[self::$fields['UF_DELIVERY_CITY_OVERLOAD_RUB']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlSeaContainerEntity::add([
            'UF_STATE_FROM' => $data[self::$cityFields['UF_DEPATURE_COUNTRY']],
            'UF_CITY_FROM' => $cityDepatureId,
            'UF_STATE_TO' => $data[self::$cityFields['UF_ARRIVAL_COUNTRY']],
            'UF_CITY_TO' => $cityArrivalId,

            'UF_SHIP_SEA_20FT_20FTW_USD' => $data[self::$fields['UF_SHIP_SEA_20FT_20FTW_USD']],
            'UF_DELIVERY_RW_20FT_RUB' => $data[self::$fields['UF_DELIVERY_RW_20FT_RUB']],
            'UF_DELIVERY_RW_20FTW_RUB' => $data[self::$fields['UF_DELIVERY_RW_20FTW_RUB']],
            'UF_SHIP_SEA_USD' => $data[self::$fields['UF_SHIP_SEA_USD']],
            'UF_DELIVERY_RW_40FT_RUB' => $data[self::$fields['UF_DELIVERY_RW_40FT_RUB']],
            'UF_DELIVERY_CITY_20FT_20FTW_RUB' => $data[self::$fields['UF_DELIVERY_CITY_20FT_20FTW_RUB']],
            'UF_DELIVERY_CITY_40FT_RUB' => $data[self::$fields['UF_DELIVERY_CITY_40FT_RUB']],
            'UF_DELIVERY_CITY_OVERLOAD_RUB' => $data[self::$fields['UF_DELIVERY_CITY_OVERLOAD_RUB']],
        ]);

        return $newRow->getId() ?? false;
    }
}
