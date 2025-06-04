<?php

namespace Object\Uhotels\Data;

use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Diag\Debug;
use CIBlockSection;
use Object\Uhotels\Settings\Settings;

class SearchHotels
{
    /**
     * Основной поиск для каталога
     *
     * @param $guests
     * @param $childrenAge
     * @param $dateFrom
     * @param $dateTo
     * @param $groupResults
     * @param $sectionIds
     * @return array
     * @throws \Exception
     */
    public static function search($guests, $childrenAge, $dateFrom, $dateTo, $groupResults, $sectionIds)
    {

        $cache = Cache::createInstance();

        // Формируем ключ кеша на основе всех параметров поиска
        $cacheKey = 'uhotels_search_' . md5(serialize([
                'guests' => $guests,
                'childrenAge' => $childrenAge,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'groupResults' => $groupResults,
                'sectionIds' => $sectionIds
            ]));

        $cacheDir = '/uhotels/search/';
        $cacheTtl = 300; // 5 минут (можно настроить)

        // Проверяем кеш
        if ($cache->initCache($cacheTtl, $cacheKey, $cacheDir)) {
            return $cache->getVars();
        }


        $tokenList = self::getUhotelsTokens($sectionIds);

        $searchData = [];
        foreach ($tokenList as $token) {
            try {
                $Search = new Search($token);
                $hotelData = $Search->findFirstAvailableRoom($dateFrom, $dateTo, $guests);

                if ($hotelData['success'] == true) {
                    $searchData[$token]['PRICE'] = $hotelData['offers'][array_key_first($hotelData['offers'])]['price'] ?? $hotelData['price'];
                    // Можно добавить дополнительную информацию
                    $searchData[$token]['ROOM_ID'] = $hotelData['room_id'] ?? null;
                    $searchData[$token]['NIGHTS'] = $hotelData['nights'] ?? null;
                    $searchData[$token]['DATES'] = $hotelData['dates'] ?? null;
                }
            } catch (Exception $e) {
                // Логируем ошибку, но продолжаем обработку других отелей
                error_log("Error searching hotel with token $token: " . $e->getMessage());
            }
        }

        // Сохраняем результат в кеш
        if ($cache->startDataCache()) {
            $cache->endDataCache($searchData);
        }

        return $searchData;
    }

    private static function getUhotelsTokens($sectionIds)
    {
        $cache = Cache::createInstance();
        $taggedCache = Application::getInstance()->getTaggedCache();
        $cacheKey = 'uhotels_tokens_' . md5(serialize($sectionIds));
        $cacheDir = '/uhotels/tokens/';
        $cacheTtl = 3600;
        // Формируем теги для инвалидации
        $tags = [
            'iblock_section_' . CATALOG_IBLOCK_ID,
            'uhotels_tokens',
        ];

        if ($sectionIds) {
            foreach ((array)$sectionIds as $sectionId) {
                $tags[] = 'section_' . $sectionId;
            }
        }

        // Проверяем кеш
        if ($cache->initCache($cacheTtl, $cacheKey, $cacheDir)) {
            return $cache->getVars();
        }

        // Начинаем тегированный кеш
        $taggedCache->startTagCache($cacheDir);

        // Устанавливаем теги
        foreach ($tags as $tag) {
            $taggedCache->registerTag($tag);
        }

        // Получаем данные из БД
        if (!$sectionIds) {
            $hotels = self::listFetch(
                [
                    'ACTIVE' => 'Y',
                    'UF_EXTERNAL_SERVICE' => Settings::UhotelsSectionPropEnumId
                ],
                ['ID' => 'ASC'],
                ['UF_EXTERNAL_ID']
            );
        } else {
            $hotels = self::listFetch(
                [
                    'ACTIVE' => 'Y',
                    'UF_EXTERNAL_SERVICE' => Settings::UhotelsSectionPropEnumId,
                    'ID' => $sectionIds
                ],
                ['ID' => 'ASC'],
                ['UF_EXTERNAL_ID']
            );
        }

        $result = array_column($hotels, 'UF_EXTERNAL_ID');

        // Завершаем тегированный кеш
        $taggedCache->endTagCache();

        // Сохраняем в обычный кеш
        if ($cache->startDataCache()) {
            $cache->endDataCache($result);
        }

        return $result;
    }

    private static function listFetch($filter = [], $order = ['ID' => 'ASC'], $select = ['*', 'PROPERTY_*']): array
    {
        $result = [];
        $res = CIBlockSection::GetList(
            $order,
            array_merge(
                array(
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                ),
                $filter
            ),
            false,
            $select,
            false
        );

        while ($element = $res->Fetch()) {
            $result[] = $element;
        }

        return $result;
    }

}