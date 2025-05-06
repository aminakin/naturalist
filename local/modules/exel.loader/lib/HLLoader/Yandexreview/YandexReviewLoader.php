<?php

namespace Exel\Loader\HLLoader\Yandexreview;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Exel\Loader\HLLoader\HLLoaderAbstract;

class YandexReviewLoader extends HLLoaderAbstract
{
    protected static string $hlCodeName = 'YandexReviews';
    protected static mixed $hlEntity;


    /**
     * example
     * тут поля городов
     *
     *      0 => 'Название',
     *      1 => 'ID',
     *      2 => 'Ссылка на Яндекс.Карты',
     *      3 => 'widget ID',
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_NAME' => 0,
        'UF_ID_OBJECT' => 1,
        'UF_ID_YANDEX' => 3,
    ];


    /**
     * @throws SystemException
     */
    public static function loadData($data)
    {
        self::$hlEntity = self::loadHL(self::$hlCodeName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if ((empty($row[0]) && $row[0] == NULL) || $row['0'] == 'ID') {
                continue;
            }

            $rowCount++;
            $result = false;

            $result = self::findRow($row);

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
     * Поиск и загрзука данных по основной таблице
     * @param $cityDepatureId
     * @param $cityArrivalId
     * @param $data
     * @return mixed
     */
    protected static function findRow($data)
    {
        $issetRow = self::$hlEntity::query()
            ->addSelect('ID')
            ->where('UF_ID_OBJECT', $data[self::$fields['UF_ID_OBJECT']])
            ->fetch();

        if ($issetRow) {
            self::$hlEntity::update($issetRow['ID'], [
                'UF_NAME' => $data[self::$fields['UF_NAME']],
                'UF_ID_YANDEX' => $data[self::$fields['UF_ID_YANDEX']],
            ]);

            return $issetRow['ID'];
        }

        $newRow = self::$hlEntity::add([
            'UF_NAME' => $data[self::$fields['UF_NAME']],
            'UF_ID_OBJECT' => $data[self::$fields['UF_ID_OBJECT']],
            'UF_ID_YANDEX' => $data[self::$fields['UF_ID_YANDEX']],
        ]);

        return $newRow->getId() ?? false;
    }
}
