<?php

namespace Exel\Loader\HLLoader;

use Bitrix\Iblock\ORM\Query;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

abstract class HLLoaderAbstract
{
    /**
     * @param $hlCode
     * @return mixed
     * @throws \Bitrix\Main\SystemException
     */
    protected static function loadHL($hlCode): mixed
    {
        return HLHelper::getHLEntity($hlCode);
    }

    /**
     * Ответ для странички
     * @return string
     */
    protected static function buildResponce($rowCount = 0, $resultCount = 0, $messageRow = []): string
    {
        return Loc::getMessage('EXEL_LOADER_RESPONCE_MESSEGE', [
            '#ROW_COUNT#' => $rowCount,
            '#RESULT_COUNT#' => $resultCount,
            '#MESSAGE_ROW#' => implode('<br>', $messageRow),
        ]);
    }
}
