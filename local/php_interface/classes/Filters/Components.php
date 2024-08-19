<?php

namespace Naturalist\Filters;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

/**
 * Работа с компонентами
 */
class Components
{
    /**
     * Возвращает ссылки из ХЛ блока, которые привязаны к конкретному фильтру
     */
    public static function getChpyLink($filter)
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        return $chpyDataClass::query()
            ->addSelect('UF_NEW_URL')
            ->where('UF_FILTER_ID', $filter)
            ?->fetch() ?? '';
    }
}
