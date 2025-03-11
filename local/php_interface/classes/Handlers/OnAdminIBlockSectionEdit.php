<?php

namespace Naturalist\Handlers;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use CIBlockSection;
use CUserFieldEnum;



class OnAdminIBlockSectionEdit
{

    private static $bnovoServiceName = 'bnovo';
    /**
     *
     * @param $arFields
     */
    public static function handle(&$arFields)
    {
        if ($arFields['IBLOCK']['ID'] == CATALOG_IBLOCK_ID) {
            return [
                "TABSET" => __CLASS__,
                "GetTabs" => array(__CLASS__, "getTab"),
                "ShowTab" => array(__CLASS__, "showTab"),
                "Action" =>  array(__CLASS__, "setAction"),
            ];
        }
    }


    public static function getTab($arArgs) {
        return [
            [
                "DIV" => preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/", '', __CLASS__ ),
                "TAB" => "Подгрузка доступности bnovo",
                "TITLE" => "Подгрузка доступности bnovo"
            ]
        ];
    }

    public static function showTab($divName, $arArgs, $bVarsFromForm)
    {
        global $APPLICATION;
        if ($divName == preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/", '', __CLASS__ )) {
            Loader::includeModule('iblock');

            $arSection = CIBlockSection::GetList([], ["IBLOCK_ID" => $arArgs['IBLOCK']['ID'], "ID" => $arArgs['ID']], false, ["UF_EXTERNAL_SERVICE", "UF_EXTERNAL_ID"]);
            if ($section = $arSection->fetch()) {
                $arExternalServicePropertyValue = CUserFieldEnum::GetList(array(), array("CODE" => "UF_EXTERNAL_SERVICE", "ID" => $section["UF_EXTERNAL_SERVICE"]))->fetch();

                if (mb_strtolower($arExternalServicePropertyValue['VALUE']) == self::$bnovoServiceName) {

                    $APPLICATION->IncludeComponent('addamant:admin.bnovoupdate', '',[
                        'ID' => $arArgs['ID'],
                        'EXTERNAL_ID' => $section['UF_EXTERNAL_ID']
                    ]);
                }


            }
        }
    }

    /**
     * После сохранения
     *
     * @param $arArgs
     * @return true
     */
    public static function setAction($arArgs)
    {
        return true;
    }

}
