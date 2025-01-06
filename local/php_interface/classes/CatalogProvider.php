<?php

namespace Naturalist;

use Bitrix\Sale;

/**
 * Товары каталога.
 */

class CatalogProvider extends \Bitrix\Catalog\Product\CatalogProvider
{
    private $hlEntity;

    public function __construct()
    {
        $this->hlEntity = new HighLoadBlockHelper('SuitPrices');
    }

    public function getProductData(array $products)
    {
        $productDataResult = parent::getProductData($products);
        $productData = $productDataResult->getData();
        static::modifyProviderProductData($productData, $this->hlEntity);
        $productDataResult->setData($productData);
        return $productDataResult;
    }

    /**
     * Устанавливает цену товара
     *
     * @param array $productData Данные о товаре из каталога     
     * 
     */
    protected static function modifyProviderProductData(array &$productData, $hlEntity)
    {
        try {
            $basketProductsMapper = [];
            foreach ($productData['PRODUCT_DATA_LIST'] as $productId => $productInfo) {
                foreach ($productInfo['PRICE_LIST'] as $basketId => $basketInfo) {
                    $basketProductsMapper[$basketId] = $productId;
                    $hlEntity->prepareParamsQuery(
                        ['*'],
                        ["ID" => "ASC"],
                        [
                            "UF_FUSERID" => Sale\Fuser::getId(),
                            "UF_PROD_ID" => $productId
                        ]
                    );
                    $rsProducts = $hlEntity->getData();
                }
            }
            if (!empty($basketProductsMapper)) {
                foreach ($basketProductsMapper as $basketItemId => $productId) {
                    if (isset($productData['PRODUCT_DATA_LIST'][$productId]['PRICE_LIST'][$basketItemId]['BASE_PRICE'])) {
                        // Проверка на сертификат с отрытой суммой
                        if ($_SESSION['CUSTOM_CERT_PRICE'] != 0 && $productId == OPEN_CERT_ELEMENT_ID) {
                            $productData['PRODUCT_DATA_LIST'][$productId]['PRICE_LIST'][$basketItemId]['BASE_PRICE'] = $_SESSION['CUSTOM_CERT_PRICE'];
                        } else {
                            $productData['PRODUCT_DATA_LIST'][$productId]['PRICE_LIST'][$basketItemId]['BASE_PRICE'] = $rsProducts['UF_PRICE'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
}
