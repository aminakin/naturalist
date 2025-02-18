<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementFeaturesdetailTable;

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
        $this->getElementInfo();
    }

    /**
     * Получает информацию о номере
     *     
     * @return void
     */
    private function getElementInfo(): void
    {
        $elements = ElementFeaturesdetailTable::getByPrimary($this->arParams['DETAIL_ID'], [
            'select' => ['ID', 'NAME', 'DETAIL_TEXT', 'PREVIEW_TEXT', 'GALERY'],
        ])->fetchCollection();

        foreach ($elements as $element) {
            if ($element->getId()) {
                foreach ($element->getGalery()->getAll() as $feature) {
                    $img[] = $feature->getValue();
                }
                $this->arResult = [
                    'NAME' => $element->getName(),
                    'TEXT' => $element->getDetailText(),
                    'DATE' => $element->getPreviewText(),
                    'IMG' => $img
                ];
            }
        }
    }

    public function executeComponent()
    {
        $this->prepareResultArray();
        $this->includeComponentTemplate();
    }
}
