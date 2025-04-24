<?php

namespace Object\Uhotels\Connector;

use Bitrix\Main\Diag\Debug;
use UHotels\ApiClient\Dto\Booking\BookingRequestCreateDto;
use UHotels\ApiClient\Dto\Booking\RequestStayDto;
use UHotels\ApiClient\Dto\Booking\ServiceInDto;
use UHotels\ApiClient\Dto\Booking\TimeInoutDto;

class UhotelsBookingData
{
    /**
     * Добавление букинга
     * @return void
     */
    public static function makeReservation($orderId, $arOrder, $arUser, $reservationPropId)
    {
        $arFields['TARIFF_ID'] = $arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['UHOTELS_TARIFF_ID']['VALUE'];
        $arFields['ROOM_ID'] = $arOrder['ITEMS'][0]['ITEM']['PROPERTIES']['EXTERNAL_ID']['VALUE'];
        $arFields['DATE_IN'] = date('Y-m-d', strtotime($arOrder['PROPS']['DATE_FROM']));
        $arFields['DATE_OUT'] = date('Y-m-d', strtotime($arOrder['PROPS']['DATE_TO']));
        $arFields['COMMENTS'] = $arOrder['FIELDS']['COMMENTS'];
        $arFields['NAME'] = $arOrder['PROPS']['NAME'];
        $arFields['EMAIL'] = $arOrder['PROPS']['EMAIL'];
        $arFields['PHONE'] = $arOrder['PROPS']['PHONE'];

        $prices = unserialize($arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['PRICES']['VALUE']);
        // привести к формату  "2025-02-25" => 1000,
        // есть
        // array (
        //  'room_id' => 1223,
        //  'start_at' => '2025-05-15',
        //  'end_at' => '2025-05-17',
        //  'occupancy_code' => 'main_p2',
        //  'amount' => 3000.0,
        //)

        // a:5:{s:7:"room_id";i:1223;s:8:"start_at";s:10:"2025-05-15";s:6:"end_at";s:10:"2025-05-17";s:14:"occupancy_code";s:7:"main_p2";s:6:"amount";d:3000;}
        $formatPrices = [];
//        foreach ($prices as $price) {
            $formatPrices[$prices['start_at']] = $prices['amount'];
//        }
        $arFields['DAYS_PRICE'] = $formatPrices;

        $arFields['ADULT'] = $arOrder["ITEMS"][0]["ITEM_BAKET_PROPS"]["GUESTS_COUNT"]['VALUE'];

        $arFields['CHILD_AGE'] = [];
        $childrenStr = $arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['CHILDREN']['VALUE'];
        if (!empty(trim($childrenStr))) {
            $arChild = explode(',', $childrenStr);
            $arFields['CHILD_AGE'] = $arChild;
        }

        $arFields['GUESTS'] = $arOrder['PROPS']['GUEST_LIST'];

        $token = $arOrder['ITEMS'][0]['ITEM']['SECTION']['UF_EXTERNAL_ID'];
        $UhotelsConnector = new UhotelsConnector($token);
        $resultBooking = $UhotelsConnector->addBooking(self::createBookingDataFromOrder($arFields));

        return $resultBooking->toArray() ?? false;
    }
    /**
     * Формирует данные для создания букинга из заказа
     *
     * @param $orderId
     * @return BookingRequestCreateDto
     */
    private static function createBookingDataFromOrder($arFields): BookingRequestCreateDto
    {
        return  BookingRequestCreateDto::fromArray([
            'room_id' => $arFields['ROOM_ID'],
            'tariff_id' => $arFields['TARIFF_ID'],
            'date_in' => $arFields['DATE_IN'],
            'date_out' => $arFields['DATE_OUT'],
            'comment' => $arFields['COMMENTS'],
            'pay_method_id' => 219, ///
            'currency' => 'RUB',
            'lang' => 'ru',
            'name' => $arFields['NAME'],
            'email' => $arFields['EMAIL'],
            'phone' => $arFields['PHONE'],

            'stays' => [
                RequestStayDto::fromArray([
                    'days_price' => $arFields['DAYS_PRICE'],
                    'adult' => $arFields['ADULT'],
                    'child_age' => $arFields['CHILD_AGE'],
                    'guests' => $arFields['GUESTS'],
                ])
            ],
        ]);
    }
}