<?php

namespace Naturalist\Filters;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Diag\Debug;

Loader::includeModule('highloadblock');

/**
 * Работа со ссылками
 */
class UrlHandler
{
    /**
     * @param $url
     * @param $siteId
     * @return array|false
     */
    public static function getByRealUrl(
        $url,
        $siteId
    ) {
        //Debug::writeToFile($url, 'getByRealUrl', '__bx_log.log');
        $chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        $result =  $chpyDataClass::query()
            ->addSelect('UF_NEW_URL')
            ->addSelect('UF_REAL_URL')
            ->AddSelect('UF_H1')
            ->AddSelect('UF_TITLE')
            ->AddSelect('UF_DESCRIPTION')
            ->where('UF_REAL_URL', $url)
            ->where('UF_ACTIVE', 1)
            ?->fetch();

        if (!empty($result)) {
            $result['SITE_ID'] = $siteId;
        }

        return $result;
    }

    /**
     * @param $url
     * @param $siteID
     * @return array|false
     */
    public static function getByNewUrl(
        $url,
        $siteID
    ) {
        $chpyDataClass = HighloadBlockTable::compileEntity(FILTER_HL_ENTITY)->getDataClass();

        $result =  $chpyDataClass::query()
            ->addSelect('UF_NEW_URL')
            ->addSelect('UF_REAL_URL')
            ->where('UF_NEW_URL', $url)
            ->where('UF_ACTIVE', 1)
            ?->fetch();

        if (!empty($result)) {
            $result['SITE_ID'] = $siteID;
        }

        return $result;
    }
}
