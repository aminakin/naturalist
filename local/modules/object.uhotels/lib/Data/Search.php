<?php

namespace Object\Uhotels\Data;

use Bitrix\Main\Diag\Debug;
use Exception;
use Object\Uhotels\Connector\UhotelsConnector;
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
            $availableRooms = [];
            foreach ($arQuotaData as $quota) {
                $availableRooms = $this->processQuota($quota);
            }

            if (!empty($availableRooms)) {
                foreach ($availableRooms as $roomId) {
                    $arItems[$roomId] = $this->processRoomData($roomId, $startDate, $endDate);
                }
            }
        }

        return [
            'arItems' => $arItems,
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
     */
    private function processRoomData($roomId, $dateFrom, $dateTo)
    {
        $offers = [];

        /** @var \UHotels\ApiClient\Dto\Tariff\TariffDto $tariff */
        foreach ($this->tariffList as $tariff) {

            $priceData = $this->processOccupancy($roomId, $dateFrom, $dateTo, $tariff->id);

//            $offers[$tariff->id] = [
//                'price' => $this->processOccupancy($roomId, $dateFrom, $dateTo, $tariff->id),
//                'tariffData' => [
//                    'id' => $tariff->id,
//                    'name' => $tariff->name,
//                    'desc' => $tariff->desc,
//                ],
//            ];
        }

        //price
        //tariffData
        // отмена
        return [
            'offers' => []
        ];
    }

    private function processOccupancy($roomId, $dateFrom, $dateTo, $tariffId): array
    {
        $occupancyData = [];
        $roomOccupancy = $this->connector->getOccupancy($dateFrom, $dateTo, $roomId, $tariffId);

        if (isset($roomOccupancy[0])){

            Debug::writeToFile(var_export($roomOccupancy[0], true));
        }

        return $occupancyData;
    }


}