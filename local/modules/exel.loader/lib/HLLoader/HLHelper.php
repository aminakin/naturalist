<?php

namespace Exel\Loader\HLLoader;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\SystemException;

class HLHelper
{
    /**
     * Получить данные HL для работы с ним
     * @param $code
     * @return mixed
     * @throws SystemException
     */
    public static function getHLEntity($code)
    {
        return HighloadBlockTable::compileEntity($code)->getDataClass();
    }

    /**
     * Удаляеи лишние NBSP из ячеек для чисел 200 000 = 200000
     * @param $string
     * @return string
     */
    public static function replaceUTFSpace($string):string
    {
        return preg_replace('/\xc2\xa0/', '', $string);
    }
}