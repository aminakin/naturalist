<?php

namespace Naturalist;

use _CIBElement;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use CCatalogDiscount;
use CCatalogProduct;
use CIBlockElement;
use CIBlockSection;
use CPrice;
use Naturalist\bronevik\BronevikSearchService;
use Object\Uhotels\Data\Search;

defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true || die();

class Products implements SearchServiceInterface
{
    /**
     * @see Traveline
     * @see Bnovo
     * @see BronevikSearchService
     * @see \Object\Uhotels\Data\Search
     */
    private const SERVICE_TYPES = [
        'traveline' => 'traveline',
        'bnovo' => 'bnovo',
        'bronevik' => 'bronevik',
//        'uhotels' => 'uhotels',
    ];

    private const DEFAULT_SELECT = [
        'IBLOCK_ID',
        'ID',
        'IBLOCK_SECTION_ID',
        'NAME',
        'CATALOG_PRICE_1',
    ];

    private SearchServiceFactory $searchServiceFactory;

    public function __construct(SearchServiceFactory $searchServiceFactory = NULL)
    {
        $this->searchServiceFactory = $searchServiceFactory;
    }

    /**
     *
     */
    public function getList(array $sort = ['SORT' => 'ASC'], array $filter = [], array $select = self::DEFAULT_SELECT): array
    {
        $this->checkIblockModule();

        $filter = array_merge([
            'IBLOCK_ID' => CATALOG_IBLOCK_ID,
            'ACTIVE' => 'Y'
        ], $filter);

        $rsItems = CIBlockElement::GetList(
            $sort,
            $filter,
            false,
            false,
            $select
        );

        $products = [];
        while ($item = $rsItems->GetNextElement()) {
            $product = $this->createProductFromElement($item);
            $products[] = $product;
        }

        return $products;
    }

    public function get(int $productId, array $select = self::DEFAULT_SELECT): array
    {
        $this->checkIblockModule();

        $item = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => CATALOG_IBLOCK_ID,
                'ID' => $productId
            ],
            false,
            false,
            $select
        )->GetNextElement();

        if (!$item) {
            return [];
        }

        $product = $this->createProductFromElement($item);
        $product['SECTION'] = $this->getProductSection($product['IBLOCK_SECTION_ID']);

        return $product;
    }

    /**
     * Генерация массива месяцев для фильтра
     *
     * @return array
     */
    public static function getDates(): array
    {
        $currentMonth = (int)date('n'); // Получаем текущий месяц (1-12)
        $year = (int)date('Y');

        // Генерируем все месяцы текущего и следующего года
        $allMonths = [];
        for ($i = 0; $i < 24; $i++) {
            $timestamp = strtotime("+{$i} month", strtotime($year . '-01-01'));
            $allMonths[] = FormatDate('f', $timestamp);
        }

        // Разделяем на текущий и следующий год
        return [
            array_slice($allMonths, $currentMonth - 1, 12), // Текущий год
            array_slice($allMonths, $currentMonth + 11, 12) // Следующий год
        ];
    }



    public  function search(
        int $guests,
        array $childrenAge,
        string $dateFrom,
        string $dateTo,
        bool $groupResults = true,
        array $sectionIds = []
    ): array {
        $resultIds = [];

        foreach (self::SERVICE_TYPES as $serviceType) {
            try {
                $service = $this->searchServiceFactory->create($serviceType);
                $resultIds[$serviceType] = $service->search(
                    $guests,
                    $childrenAge,
                    $dateFrom,
                    $dateTo,
                    $groupResults,
                    $sectionIds
                );
            } catch (\Exception $e) {
                // Логирование ошибки
                continue;
            }
        }

        return $groupResults
            ? $resultIds
            : array_replace([], ...array_values($resultIds));
    }

    public function searchRooms(
        int $sectionId,
        string $externalId,
        string $serviceType,
        int $guests,
        array $childrenAge,
        string $dateFrom,
        string $dateTo,
        int $minChildAge = 0
    ): array {
        if (empty($externalId)) {
            return ['arRooms' => [], 'error' => 'Invalid external ID'];
        }

        try {
            $service = $this->searchServiceFactory->create($serviceType);
            return $service->searchRooms(
                $sectionId,
                $externalId,
                $serviceType,
                $guests,
                $childrenAge,
                $dateFrom,
                $dateTo,
                $minChildAge
            );
        } catch (\Exception $e) {
            return [
                'arRooms' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    public static function getDiscount(int $productId, float $price, array $userGroups): array
    {
        if ($price <= 0) {
            return [];
        }

        $discounts = CCatalogDiscount::GetDiscountByProduct(
            $productId,
            $userGroups,
            'N'
        );

        if (empty($discounts)) {
            return [];
        }

        $discountPrice = CCatalogProduct::CountPriceWithDiscount(
            $price,
            'RUB',
            $discounts
        );

        return [
            'DISCOUNT_PRICE' => $discountPrice,
            'DISCOUNT_PERCENT' => (($price - $discountPrice) / $price) * 100,
        ];
    }

    public static function setQuantity(int $productId, int $quantity = 999): bool
    {
        return CCatalogProduct::Add([
            'ID' => $productId,
            'QUANTITY' => $quantity,
        ]);
    }

    public static function setPrice(int $productId, float $price = 10000.0, string $currency = 'RUB'): void
    {
        $priceFields = [
            'PRODUCT_ID' => $productId,
            'CATALOG_GROUP_ID' => 1,
            'PRICE' => $price,
            'CURRENCY' => $currency,
        ];

        $existingPrice = CPrice::GetList([], ['PRODUCT_ID' => $productId, 'CATALOG_GROUP_ID' => 1])->Fetch();
        if ($existingPrice) {
            CPrice::Update($existingPrice['ID'], $priceFields);
        } else {
            CPrice::Add($priceFields);
        }
    }

    private function createProductFromElement(_CIBElement $element): array
    {
        $product = $element->GetFields();
        $product['PROPERTIES'] = $element->GetProperties();
        return $product;
    }

    private function getProductSection(int $sectionId): array
    {
        $section = CIBlockSection::GetList(
            [],
            ['IBLOCK_ID' => CATALOG_IBLOCK_ID, 'ID' => $sectionId],
            false,
            ['ID', 'NAME', 'SECTION_PAGE_URL', 'UF_*']
        )->GetNext();

        if ($section) {
            $section['RATING'] = Reviews::getCampingRating($sectionId)[$sectionId] ?? 0;
        }

        return $section ?: [];
    }

    private function checkIblockModule(): void
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Iblock module is not installed');
        }
    }
}