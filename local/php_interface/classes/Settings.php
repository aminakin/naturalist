<?

namespace Naturalist;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;
use CModule;
use CIBlockElement;
use CFile;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

class Settings
{
    /* Получение настроек */
    public static function get() {
        $cache = Cache::createInstance();
        $taggedCache = Application::getInstance()->getTaggedCache();

        $cachePath = 'settings';
        $cacheTtl  = 86400;
        $cacheId   = 'settings';

        if($cache->initCache($cacheTtl, $cacheId, $cachePath)) {
            $vars = $cache->getVars();
            $arSettings = $vars["settings"];

        } elseif($cache->startDataCache()) {
            CModule::IncludeModule('iblock');

            $rsSettings = CIBlockElement::GetList(false, array("IBLOCK_ID" => SETTINGS_IBLOCK_ID), false, false, array("ID", "CODE", "PREVIEW_TEXT", "PROPERTY_FILE"));
            $arSettings = array();
            while($arSetting = $rsSettings->GetNext()) {
                if ($arSetting["PREVIEW_TEXT"]) {
                    $value = $arSetting["PREVIEW_TEXT"];

                } elseif ($arSetting["PROPERTY_FILE_VALUE"]) {
					$value = CFile::GetFileArray($arSetting["PROPERTY_FILE_VALUE"][0])["SRC"];
                }

                $arSettings[$arSetting["CODE"]] = $value;
            }

            $taggedCache->startTagCache($cachePath);
            $taggedCache->registerTag('iblock_id_'.SETTINGS_IBLOCK_ID);
            $taggedCache->endTagCache();
            $cache->endDataCache(array(
                "settings" => $arSettings
            ));
        }

        return $arSettings;
    }

	public static function getMainBg() {
		$cache = Cache::createInstance();

        $cachePath = 'settings';
        $cacheTtl  = 600;
        $cacheId   = 'timers';

        if($cache->initCache($cacheTtl, $cacheId, $cachePath)) {
            $vars = $cache->getVars();
            $srcMainBg = $vars["timers"];

        } elseif ($cache->startDataCache()) {
            CModule::IncludeModule('iblock');

            $arTimer = CIBlockElement::GetList(false,
                array("IBLOCK_ID" => SETTINGS_IBLOCK_ID, "CODE" => "main_bg", "!PROPERTY_FILE" => false, "PROPERTY_TIMER_VALUE" => "Y"),
                false, false, array("ID", "CODE", "PREVIEW_TEXT", "PROPERTY_FILE", "PROPERTY_TIMER"))->Fetch();
            $srcMainBg = '';

            if (isset($arTimer["PROPERTY_FILE_VALUE"]) && !empty($arTimer["PROPERTY_FILE_VALUE"])) {
                if(count($arTimer["PROPERTY_FILE_VALUE"]) > 1) {
                    $index = rand(0, count($arTimer["PROPERTY_FILE_VALUE"]) - 1);
                } else {
                    $index = 0;
                }

                $value = CFile::GetFileArray($arTimer["PROPERTY_FILE_VALUE"][$index])["SRC"];

                $srcMainBg = $value;
            }

            $cache->endDataCache(array(
                "timers" => $srcMainBg
            ));
        }

        return $srcMainBg;
	}

    /* Получение табов на главной */
    public static function getTabsMain() {
        $cache = Cache::createInstance();
        $taggedCache = Application::getInstance()->getTaggedCache();

        $cachePath = 'tabs';
        $cacheTtl  = 86400;
        $cacheId   = 'tabs';

        if($cache->initCache($cacheTtl, $cacheId, $cachePath)) {
            $vars = $cache->getVars();
            $arTabs = $vars["tabs"];

        } elseif($cache->startDataCache()) {
            CModule::IncludeModule('iblock');

            $rsTabs = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => TABS_IBLOCK_ID, "ACTIVE" => "Y"), false, false, array("ID", "CODE", "NAME"));
            $arTabs = array();
            while($arTab = $rsTabs->GetNext()) {
                $arTabs[$arTab["CODE"]] = $arTab;
            }

            $taggedCache->startTagCache($cachePath);
            $taggedCache->registerTag('iblock_id_'.TABS_IBLOCK_ID);
            $taggedCache->endTagCache();
            $cache->endDataCache(array(
                "tabs" => $arTabs
            ));
        }

        return $arTabs;
    }
}