<?php

namespace Naturalist\Certificates;

use Bitrix\Main\Loader;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Catalog\PriceTable;
use Bitrix\Iblock\Elements\ElementCertificatesTable;
use Naturalist\HighLoadBlockHelper;

/**
 * Подготовка данных по сертификатам для компонента.
 */

class CatalogHelper
{    
    public $hlVariantsValues;
    public $hlPocketsValues;
    public $hlElVariantsValues;

    public function __construct()
    {
        $this->hlVariantsValues = $this->getHlPropData(new HighLoadBlockHelper('Certvar'));
        $this->hlPocketsValues = $this->getHlPropData(new HighLoadBlockHelper('Certpocket'));
        $this->hlElVariantsValues = $this->getHlPropData(new HighLoadBlockHelper('Certelvar'));
    }

    /**
     * Возвращает массив сертификатов
     *
     * @return array
     * 
     */
    public function getProducts() : array
    {
        $result = [];
        $elements = ElementCertificatesTable::getList([
            'order' => ['SORT' => 'ASC'],
            'select' => ['NAME', 'ID', 'PRICETABLE', 'VARIANT', 'VARIANT_EL', 'POCKET', 'PRICE_COLOR'],
            'filter' => ['=ACTIVE' => 'Y'],
            'runtime' => [
                new ReferenceField(
                    'PRICETABLE',
                    PriceTable::class,
                    ['=this.ID' => 'ref.PRODUCT_ID'],
                    ['join_type' => 'RIGHT']
                )
            ]
        ])->fetchCollection();

        foreach ($elements as $element) {            
            $result[] =  [
                'ID' => $element->getId(),
                'PRICE' => $element->get('PRICETABLE')->get('PRICE'),
                'COLOR' => $element->getPriceColor()->getValue(),
                'VARIANT' => $this->getMultiProp($element->getVariant()->getAll(), $this->hlVariantsValues),
                'POCKET' => $this->getMultiProp($element->getPocket()->getAll(), $this->hlPocketsValues),
                'VARIANT_EL' => $this->getMultiProp($element->getVariantEl()->getAll(), $this->hlElVariantsValues),
            ];
        }

        return $result;
    }

    /**
     * Возвращает массив значений множественного свойства вместе с доп. полями из HL блока
     *
     * @param array $arProp
     * @param array $hlValues
     * 
     * @return array
     * 
     */
    private function getMultiProp(array $arProp, array $hlValues) : array
    {
        $propsVals = [];

        foreach ($arProp as $prop) {
            $propsVals[$prop->getValue()] = $hlValues[$prop->getValue()];
        }

        return $propsVals;
    }

    /**
     * Возвращает массив записей справочника, где ключами будут XML_ID
     *
     * @param HighLoadBlockHelper $entity
     * 
     * @return array
     * 
     */
    private function getHlPropData(HighLoadBlockHelper $entity) :array
    {
        $result = [];
        $entity->prepareParamsQuery(
            [
                "ID",
                "UF_XML_ID",
                "UF_FILE",
                "UF_NAME",                
            ],            
            [
                "ID" => "ASC"
            ],
            [],
        );

        $hlArray = $entity->getDataAll();

        if (is_array($hlArray) && !empty($hlArray)) {
            foreach ($hlArray as $hlValue) {
                $result[$hlValue['UF_XML_ID']] = $hlValue;
            }
        }

        return $result;
    }
}