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

    /**
     * Поиск ЧПУ ссылок по коду ссылки
     */
    public static function getChpyLinkByUrl($filter)
    {
        $chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        return $chpyDataClass::query()
            ->addSelect('UF_SEO_TEXT')
            ->addSelect('UF_CANONICAL')
            ->where('UF_NEW_URL', $filter)
            ?->fetch() ?? '';
    }
}
