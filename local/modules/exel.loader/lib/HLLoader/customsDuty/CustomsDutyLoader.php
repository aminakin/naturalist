<?php

namespace Calculator\Kploader\HLLoader\customsDuty;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Calculator\Kploader\HLLoader\HLHelper;
use Calculator\Kploader\HLLoader\HLLoaderAbstract;

class CustomsDutyLoader extends HLLoaderAbstract
{

    protected static string $hlCustomsDutyName = 'CustomsDuty';
    protected static mixed $hlCustomsDutyEntity;
    /**
     * example
     * 0 => '200001' - ТС MIN
     * 1 => '450000' - ТС MAX
     * 2 => '1550' -Таможенный сбор, руб
     *
     * соотношение столбцов CSV и имен в БД
     * uf_field =>  номер столбца
     **/
    protected static $fields = [
        'UF_MIN' => 0,
        'UF_MAX' => 1,
        'UF_VALUE' => 2,
    ];

    public static function loadData($data)
    {
        self::$hlCustomsDutyEntity = self::loadHL(self::$hlCustomsDutyName);

        $rowCount = $resultCount = 0;
        foreach ($data as $row) {
            if (empty($row[0]) && $row[0] == NULL) {
                continue;
            }

            $rowCount++;
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
     * Поиск и загрзука данных по основной таблице Таможенный сбор
     * @param $data
     * @return mixed
     */
    private static function findRow($data)
    {
        if (strlen($data[self::$fields['UF_MIN']]) > 0) {
            $ufMin = HLHelper::replaceUTFSpace($data[self::$fields['UF_MIN']]);
            $data[self::$fields['UF_MIN']] = $ufMin > 0 ? $ufMin : 0;
        }

        if (strlen($data[self::$fields['UF_MAX']]) > 0) {
            $ufMax = HLHelper::replaceUTFSpace($data[self::$fields['UF_MAX']]);
            $data[self::$fields['UF_MAX']] = $ufMax > 0 ? $ufMax : 0;
        }

        $data[self::$fields['UF_VALUE']] = HLHelper::replaceUTFSpace($data[self::$fields['UF_VALUE']]);


        $issetRow = self::$hlCustomsDutyEntity::query()
            ->addSelect('ID')
            ->where('UF_MIN', $data[self::$fields['UF_MIN']])
            ->where('UF_MAX', $data[self::$fields['UF_MAX']])
            ->fetch();


        if ($issetRow) {
            self::$hlCustomsDutyEntity::update($issetRow['ID'], [
                'UF_VALUE' => $data[self::$fields['UF_VALUE']],
            ]);

            return $issetRow['ID'];
        }


        $newRow = self::$hlCustomsDutyEntity::add([
            'UF_MIN' => $data[self::$fields['UF_MIN']],
            'UF_MAX' => $data[self::$fields['UF_MAX']],
            'UF_VALUE' => $data[self::$fields['UF_VALUE']],
        ]);

        return $newRow->getId() ?? false;
    }
}
