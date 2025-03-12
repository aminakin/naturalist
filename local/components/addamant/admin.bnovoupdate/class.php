<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Context;
use Bitrix\Main\Type\Date;
use Naturalist\Bnovo;
use Naturalist\Certificates\CatalogHelper;
use Naturalist\Certificates\OrderHelper;
use Naturalist\Orders;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Delivery\Services;
use Bitrix\Sale;

Loc::loadMessages(__FILE__);

class AdminBnovoupdate extends \CBitrixComponent implements Controllerable
{
    /**
     * @inerhitDoc
     */
    public function configureActions(): array
    {
        return [
            'updateBnovo' => [
                'prefilters' => [],
            ],
        ];
    }

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    /**
     * @return void
     */
    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

    /**
     * Обновление данных Бново по кнопке
     *
     * @param $dateFromValue
     * @param $dateToValue
     * @param $externalId
     * @return array
     */
    public function updateBnovoAction($dateFromValue, $dateToValue, $externalId): array
    {

        if ($dateToValue != '' && $dateFromValue != '' && intval($externalId) > 0) {

            $startDate = date('Y-m-d', strtotime($dateFromValue));
            $endDate = date('Y-m-d', strtotime($dateToValue));

            $bnovo = new Bnovo;
            $resultReservation = $bnovo->updateReservationData(intval($externalId), [], [], [$startDate, $endDate]);

            if ($resultReservation != null) {
                return [
                    'success' => false,
                    'message' => $resultReservation
                ];
            }
            $resultAvailability = $bnovo->updateAvailabilityData(intval($externalId), [], [$startDate, $endDate]);

            if ($resultAvailability != null) {

                return [
                    'success' => false,
                    'message' => $resultAvailability
                ];
            }

            return [
                'success' => true,
                'message' => 'Данные обновлены'
            ];
        }

        return [
            'success' => false,
            'message' => 'Некорректно введены диапазоны дат'
        ];
    }

}

