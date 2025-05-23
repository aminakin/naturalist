<?php

namespace Object\Uhotels\Data;

use Bitrix\Main\Diag\Debug;
use CIBlockElement;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Object\Uhotels\Connector\UhotelsConnector;
use Object\Uhotels\Enum\OccupancyEnum;
use UHotels\ApiClient\Dto\Quota\QuotaDto;

class Search
{

    private UhotelsConnector $connector;

    private Array $tariffList;

    /**
     * @throws \Exception
     */
    public function __construct($token)
    {
        if (empty($token)) {
            throw new Exception('API token is required for UHotelsConnector.');
        }

        $this->connector = new UhotelsConnector($token);
        $this->tariffList = $this->connector->getTariffList();
    }

    /**
     * @param $sectionId - ид раздела в каталоге битрикса
     * @param $externalId - внешний ИД отеля в uhotels
     * @param $guests - кол-во гостей
     * @param $arChildrenAge - массив. [8,4] - возраст детей
     * @param $dateFrom - Дата от
     * @param $dateTo - Дата до
     * @param $minChildAge - null - минимальный возраст ребенка
     * @return array
     */
    public function searchHotels($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo, $minChildAge): array
    {
        $startDate = date('Y-m-d', strtotime($dateFrom));
        $endDate = date('Y-m-d', strtotime($dateTo));

        $arQuotaData = $this->connector->getQuota($startDate, $endDate);

        $arItems = [];
        if (!empty($arQuotaData) && $arQuotaData['0'] != NULL) {

            $rsElements = CIBlockElement::GetList(
                ["ID" => "ASC"],
                [
                    "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                    "ACTIVE"    => "Y",
                    "SECTION_ID" => $sectionId,
                ],
                false,
                false,
                ["IBLOCK_ID", "ID", "PROPERTY_EXTERNAL_ID", "PROPERTY_EXTERNAL_CATEGORY_ID"]
            );

            $arElementsIDs = [];
            while ($arElement = $rsElements->Fetch()) {
                $arElementsIDs[$arElement["PROPERTY_EXTERNAL_ID_VALUE"]] = $arElement["ID"];
            }

            $availableRooms = [];
            foreach ($arQuotaData as $quota) {
                $availableRooms = $this->processQuota($quota);
            }

            // Если есть квота по номеру и его можно забронировать на выбранные дни
            if (!empty($availableRooms)) {
                foreach ($availableRooms as $roomId) {
                    $offers = $this->processRoomData($roomId, $startDate, $endDate, $guests, $arChildrenAge, $minChildAge);

                    //если номер доступен и по нему есть цены
                    if (!empty($offers)) {
                        $elementId = $arElementsIDs[$roomId];
                        if ($elementId) {
                            Debug::writeToFile(var_export($elementId, true), true);
                            $arItems[$elementId] = ['offers' => $offers];
                        }

                    }
                }
            }
        }

        return [
            'arRooms' => $arItems,
            'error' => !count($arItems) ? 'Не найдено номеров на выбранные даты' : '',
        ];
    }

    /**
     * Вычисляет доступен ли номер на весь период
     *
     * @param QuotaDto $quotaData
     * @return array
     */
    private function processQuota(QuotaDto $quotaData): array
    {
        $roomsId = [];
        foreach ($quotaData->quotas as $quota) {
            /** @var \UHotels\ApiClient\Dto\Quota\QuotaDetailDto $quota */
            $hasZeroQuota = array_reduce($quota->quota, function ($carry, $roomQuota) {
                // Eсли хоть на одну дату вернулся 0, то в аккум ставим 0. И для его сохранения до конца так же оставляем в ретурн
                // На каждой итерации результат выражения ($carry || $roomQuota->quota === 0) становится новым значением $carry
                return $carry || $roomQuota->quota === 0;
            }, false);

            if (!$hasZeroQuota) {
                $roomsId[] = $quota->room_id;
            }
        }

        return $roomsId;
    }


    /**
     * Получение информации для вывода в карточку объекта
     *
     * @param $roomId
     * @param $dateFrom
     * @param $dateTo
     * @return array
     * @throws GuzzleException
     */
    private function processRoomData($roomId, $dateFrom, $dateTo, $guests, $arChildrenAge, $minChildAge)
    {
        $offers = [];

        // Вычисляем количество ночей
        $startDate = new DateTime($dateFrom);
        $endDate = new DateTime($dateTo);
        $interval = $startDate->diff($endDate);
        $nights = $interval->days;

        if ($nights <= 0) {
            return [];
        }

        /** @var \UHotels\ApiClient\Dto\Tariff\TariffDto $tariff */
        foreach ($this->tariffList as $tariff) {

            $occupancyData = $this->processOccupancy($roomId, $dateFrom, $dateTo, $tariff->id);

            if (!empty($occupancyData)) {
                foreach ($occupancyData as $occupancyCode => $occupancyPriceData) {

                    if (OccupancyEnum::getValueByCode($occupancyCode) == (int)$guests) {
                        /** @var \UHotels\ApiClient\Dto\Occupancy\OccupancyDetailDto $occupancyPriceData */

                        // Умножаем цену за ночь на количество ночей
                        $totalPrice = $occupancyPriceData->amount * $nights;

                        $offers[$tariff->id] = [
                            'price' => $totalPrice,
                            'price_per_night' => $occupancyPriceData->amount,
                            'nights' => $nights,
                            'tariffData' => [
                                'id' => $tariff->id,
                                'name' => $tariff->name,
                                'desc' => $tariff->desc,
                                'penalty' => $this->connector->getPenaltyById($tariff->penalty)?->toArray() ?? null,
                            ],
                            'days_price' => $occupancyPriceData->toArray(),
                        ];

                    }
                }
            }
        }

        return $offers;
    }

    /**
     * Получает и фильтрует данные о стоимости проживания (occupancy) для конкретного номера и тарифа в заданный период.
     *
     * @param $roomId
     * @param $dateFrom
     * @param $dateTo
     * @param $tariffId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function processOccupancy($roomId, $dateFrom, $dateTo, $tariffId): array
    {
        $occupancyData = [];
        $roomOccupancy = $this->connector->getOccupancy($dateFrom, $dateTo, $roomId, $tariffId);

        // Проверяем, что данные получены и есть массив occupancies
        if (!empty($roomOccupancy[0]->occupancies)) {
            foreach ($roomOccupancy[0]->occupancies as $occupancyList) {
                // Проверяем, что это нужный тариф
                if ($occupancyList->tariff_id == $tariffId) {
                    // Проверяем, что есть данные по занятости
                    if (!empty($occupancyList->occupancy)) {
                        foreach ($occupancyList->occupancy as $detail) {
                            /**
                             * @var \UHotels\ApiClient\Dto\Occupancy\OccupancyDetailDto $detail
                             */
                            // Добавляем только записи с положительной стоимостью
                            if ($detail->amount > 0) {
                                $occupancyData[$detail->occupancy_code] = $detail;
                            }
                        }
                    }
                }
            }
        }

        return $occupancyData;
    }


}