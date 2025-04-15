<?php

namespace Object\Uhotels\Connector;

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
        $guestItem = current($arOrder['PROPS']['GUEST_LIST']);
        $arGuestItem = explode(' ', $guestItem, 2);
        $arGuestItem = ['lastName' => $arGuestItem[0], 'firstName' => $arGuestItem[1]];
        $guests = [];
        for ($i = 0; $i < $arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['GUESTS_COUNT']['VALUE']; $i++) {
            $guests[] = $arGuestItem;
        }

        $children = [];
        $childrenStr = $arOrder['ITEMS'][0]['ITEM_BAKET_PROPS']['CHILDREN']['VALUE'];
        if (!empty(trim($childrenStr))) {
            $arChild = explode(',', $childrenStr);
            foreach ($arChild as $childItem) {
                $children[] = ['count' => 1, 'age' => $childItem];
            }
        }

//        $this->bronevik->setAttempt(2);
//        $this->bronevik->setCheckBeforeAttemptCallback(function ($arguments, $attempt) use ($orderId, $reservationPropId) {
//            if (($order = $this->bronevik->searchOrderByReferenceId($orderId)) === null) {
//                return true;
//            }
//
//            $this->setExternalIdToOrder($orderId, $order->getId(), $reservationPropId);
//
//            return false;
//        });
//        $orderResult = $this->bronevik->OrderCreate($orderId, $offerCode, $guests, $children);
//        $this->bronevik->setCheckBeforeAttemptCallback(null);
//
//        return $this->setExternalIdToOrder($orderId, $orderResult->getId(), $reservationPropId);
    }
    /**
     * Формирует данные для создания букинга из заказа
     *
     * @param $orderId
     * @return BookingRequestCreateDto
     */
    private static function createBookingDataFromOrder($orderId): BookingRequestCreateDto
    {

        $bookingCreateData = BookingRequestCreateDto::fromArray([
            'room_id' => 1223,
            'tariff_id' => 28,
            'date_in' => "2025-02-25",
            'date_out' => "2025-02-28",
            'comment' => 'commentary',

            'currency' => 'RUB',
            'lang' => 'ru',
            'name' => 'Создание Тест',
            'email' => 'test@create.test',
            'phone' => '999999999',

            'stays' => [
                RequestStayDto::fromArray([
                    'days_price' => [
                        "2025-02-25" => 1000,
                        "2025-02-26" => 2000,
                        "2025-02-27" => 3000,
                    ],
                    'adult' => 2,
                    'child_age' => [
                        0,
                        "2",
                        "1",
                    ],
                    'guests' => [
                        "Тестовый Создатель",
                        "Второй тестовый"
                    ],
                ])
            ],
        ]);

        return new BookingRequestCreateDto();
    }
}