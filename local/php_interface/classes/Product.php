<?

namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use CIBlockElement;
use CIBlockSection;
use CUser;
use CFile;
use CCatalogDiscount;
use CCatalogProduct;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

class Products
{
    private static $travelinePropEnumXmlId = 'traveline';
    private static $bnovoPropEnumXmlId = 'bnovo';

    /* Получение товаров */
    public function getList($arSort = false, $arFilter = false, $arSelect = false) {
        if(!$arSort) {
            $arSort = array("SORT" => "ASC");
        }

        if(!$arFilter) {
            $arFilter = array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y");
        } else {
            $arFilter = array_merge(array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ACTIVE" => "Y"), $arFilter);
        }

        if(!$arSelect) {
            $arSelect = array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "CATALOG_PRICE_1");
        }

        $rsItems = CIBlockElement::GetList(
            $arSort,
            $arFilter,
            false,
            false,
            $arSelect
        );

        $arProducts = array();
        while($obItem = $rsItems->GetNextElement()) {
            $arProduct = $obItem->GetFields();
            $arProduct['PROPERTIES'] = $obItem->GetProperties();

            $arProducts[] = $arProduct;
        }

        return $arProducts;
    }

    /* Получение товара по Id */
    public function get($productId, $arSelect = false) {
        if(!$arSelect) {
            $arSelect = array("IBLOCK_ID", "ID", "IBLOCK_SECTION_ID", "NAME", "CATALOG_PRICE_1");
        }

        $obItem = CIBlockElement::GetList(
            false,
            array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "ID" => intval($productId)
            ),
            false,
            false,
            $arSelect
        )->GetNextElement();

        if($obItem) {
            $arProduct = $obItem->GetFields();
            $arProduct['PROPERTIES'] = $obItem->GetProperties();

            $arProduct['SECTION'] = CIBlockSection::GetList(array(), array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $arProduct["IBLOCK_SECTION_ID"]), false, array("ID", "NAME", "SECTION_PAGE_URL", "UF_*"), false)->GetNext();
            $arProduct['SECTION']['RATING'] = Reviews::getCampingRating($arProduct['SECTION']['ID'])[$arProduct['SECTION']['ID']];
        }
        return $arProduct ?? array();
    }

    /* Генерация массива месяцев для фильтра */
    public static function getDates() {
        $arDates = array();

        $currMonth = date('m');
        for ($i = $currMonth; $i <= 12; $i++) {
            $arDates[0][] = FormatDate("f", strtotime('1970-' . $i . '-01'));
        }
        for ($j = 1; $j <= 12; $j++) {
            $arDates[1][] = FormatDate("f", strtotime('1970-' . $j . '-01'));
        }

        return $arDates;
    }

    /* Получение списка свободных объектов в выбранный промежуток */
    public static function search($guests, $arChildrenAge, $dateFrom, $dateTo, $isGroup = true, $sectionIds = []) {
        // Traveline
        $arResultIDs["traveline"] = Traveline::search($guests, $arChildrenAge, $dateFrom, $dateTo, $sectionIds);
        // Bnovo
        $bnovo = new Bnovo();
        $arResultIDs["bnovo"] = $bnovo->search($guests, $arChildrenAge, $dateFrom, $dateTo);

        return ($isGroup) ? $arResultIDs : $arResultIDs["traveline"] + $arResultIDs["bnovo"];
    }

    /* Получение списка свободных номеров объекта в выбранный промежуток */
    public static function searchRooms($sectionId, $externalId, $serviceType, $guests, $arChildrenAge, $dateFrom, $dateTo) {
        if(!$externalId) {
            return false;
        }

        if($serviceType == self::$travelinePropEnumXmlId) { // Traveline
            $arRooms = Traveline::searchRooms($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo);

        } elseif($serviceType == self::$bnovoPropEnumXmlId) { // Bnovo
            $bnovo = new Bnovo();
            $arRooms = $bnovo->searchRooms($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo);
        }

        return $arRooms;
    }
    
    /**
     * Возвращает данные по скидке
     *
     * @param int $prodId ID товара.
     * 
     * @return array Массив данных о скидке на товар
     * 
     */
    public static function getDiscount ($prodId, $price) {
        global $USER;

        $arDiscounts = CCatalogDiscount::GetDiscountByProduct($prodId, $USER->GetUserGroupArray(), "N");        
        
        if (count($arDiscounts)) {
            $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                $price,
                'RUB',
                $arDiscounts
            );
            $percent = intval($discountPrice) / intval($price) * 100;
            return [
                'DISCOUNT_PRICE' => $discountPrice,
                'DISCOUNT_PERCENT' => $percent,
            ];
        }

        return [];
    }
}