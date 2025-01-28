<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementGlampingsTable;
use Bitrix\Highloadblock\HighloadBlockTable;

/**
 * Класс компонента получения информации о номере
 *
 * @package Bitrix
 */
class ObjectInfo extends \CBitrixComponent
{
    /**
     * @var string Название поля со списком категорий удобств.
     */
    private const PROPERTY_LIST_NAME = 'UF_CATEGORY';

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    /**
     * Подготовка arResult
     *     
     * @return void
     */
    protected function prepareResultArray(): void
    {
        Loader::includeModule("iblock");
        $this->getUserFieldListValues();
        $this->getElementInfo();
        $this->getHlBlockValues();
        $this->prepareProps();
    }

    /**
     * Распределяе полученные удобства номера по категориям
     *     
     * @return void
     */
    private function prepareProps(): void
    {
        if ($this->arResult['FEATURE_CATEGORIES'] && $this->arResult['SELECTED_FEATURES']) {
            foreach ($this->arResult['SELECTED_FEATURES'] as $feature) {
                $this->arResult['FEATURE_CATEGORIES'][$feature['UF_CATEGORY']]['LIST'][] = $feature;
            }
        }
    }

    /**
     * Получает информацию о номере
     *     
     * @return void
     */
    private function getElementInfo(): void
    {
        $element = ElementGlampingsTable::getByPrimary($this->arParams['ROOM_ID'], [
            'select' => ['ID', 'NAME', 'DETAIL_TEXT', 'FEATURES'],
        ])->fetchObject();

        if ($element->getId()) {
            foreach ($element->getFeatures()->getAll() as $feature) {
                $features[] = $feature->getValue();
            }
            $this->arResult['ELEMENT'] = [
                'NAME' => $element->getName(),
                'TEXT' => $element->getDetailText(),
                'FEATURES' => $features,
            ];
        }
    }

    /**
     * Получает все значения поля типа список
     *     
     * @return void
     */
    private function getUserFieldListValues(): void
    {
        $result = [];

        $query = \CUserFieldEnum::GetList(array(), array(
            "USER_FIELD_NAME" => self::PROPERTY_LIST_NAME
        ));
        while ($row = $query->Fetch()) {
            $result[$row['ID']] = $row;
        }
        $this->arResult['FEATURE_CATEGORIES'] = $result;
    }

    /**
     * Получает все значения HL блока
     *     
     * @return void
     */
    private function getHlBlockValues(): void
    {
        if ($this->arResult['ELEMENT']['FEATURES']) {
            $entityDataClass = HighloadBlockTable::compileEntity(ROOM_FEATURES_HL_ENTITY)->getDataClass();
            $query = $entityDataClass::query()
                ->addSelect('ID')
                ->addSelect('UF_NAME')
                ->addSelect('UF_CATEGORY')
                ->where('UF_XML_ID', 'in', $this->arResult['ELEMENT']['FEATURES'])
                ->whereNotNull('UF_CATEGORY')
                ?->fetchAll();
            $this->arResult['SELECTED_FEATURES'] = $query;
        }
    }

    public function executeComponent()
    {
        $this->prepareResultArray();
        $this->includeComponentTemplate();
    }
}
