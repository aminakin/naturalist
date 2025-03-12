<?php

namespace Naturalist\bronevik;

use Bitrix\Main\AccessDeniedException;
use Bronevik\HotelsConnector\Element\AvailableMeal;
use Bronevik\HotelsConnector\Element\CancellationPolicy;
use Bronevik\HotelsConnector\Element\Child;
use Bronevik\HotelsConnector\Element\Guest;
use Bronevik\HotelsConnector\Element\Order;
use Bronevik\HotelsConnector\Element\OrderServiceAccommodation;
use COption;
use Naturalist\bronevik\enums\RoomTypeEnum;
use Naturalist\bronevik\enums\StatusOrderEnum;
use Naturalist\bronevik\repository\Bronevik;

class OrderChangeBronevik
{
    private readonly Bronevik $bronevik;
    private readonly OrderChangeLog $orderChangeLog;
    private readonly OrderChangeStatus $orderChangeStatus;
    public function __construct() {
        $this->bronevik = new Bronevik();
        $this->orderChangeLog = new OrderChangeLog();
        $this->orderChangeStatus = new OrderChangeStatus();
    }

    /**
     * @throws AccessDeniedException
     */
    public function __invoke(array $header, array $data)
    {
        $changeData = [];
        if ($this->checkSha($header, $data)) {
            $changeData[] = 'Источник: '.$data['event']['changeSource'];

            $externalOrderId = $data['data']['orderId'];
            $externalServiceId = $data['data']['serviceId'];
            $orderId = $data['data']['referenceId'];
            $changes = $data['data']['changes'];
            $additionalChanges = $data['data']['additionalChanges'];

            $order = $this->bronevik->getOrder($externalOrderId);

            $this->appendChanges($changeData, $changes, $order, $externalServiceId);
            $this->appendAdditionalChanges($changeData, $additionalChanges, $order, $externalServiceId);
            $this->changeStatus($orderId, $changes, $order, $externalServiceId);

            $this->orderChangeLog->store($orderId, $data['event']['timestamp'], $changeData);
            $this->sendNotification($orderId, $changeData);
        } else {
            throw new AccessDeniedException();
        }
    }

    private function sendNotification($orderId, $changeData): void
    {
        $arEventFields = array(
            "ORDER_ID" => $orderId,
            "CONTENT" => implode("<br/>", $changeData),
        );

        \CEvent::Send("BRONEVIK_ORDER_CHANGE", SITE_ID, $arEventFields);
    }

    private function changeStatus(int $orderId, array $changes, Order $externalOrder, int $externalServiceId)
    {
        if (in_array('serviceStatus', $changes)) {
            foreach ($externalOrder->getServices()->getService() as $orderService) {
                if ($orderService->id == $externalServiceId) {
                    ($this->orderChangeStatus)($orderId, $orderService->statusId);
                }
            }
        }
    }

    private function appendAdditionalChanges(array &$changeData, ?array $changes, Order $order, int $externalServiceId)
    {
        foreach ($changes as $change) {
            if ($change['type'] == 'GuestCheckOutStatus') {
                switch ($change['value']) {
                    case 'Confirmed':
                        $changeData[] = 'Отель подтвердил выезд гостя';
                        break;
                    case 'Confirmed.PossibleChangesInService':
                        $changeData[] = 'Отель подтвердил выезд гостя с изменениями в услуге';
                        break;
                    case 'AccommodationExtended':
                        $changeData[] = 'Проживание продлено';
                        break;
                    case 'NoShowWithoutPenalty':
                        $changeData[] = 'Незаезд без штрафа';
                        break;
                    case 'NoShowWithPenalty':
                        $changeData[] = 'Незаезд со штрафом';
                        break;
                }
            }
        }
    }
    private function appendChanges(array &$changeData, array $changes, Order $order, int $externalServiceId): void
    {
        /** @var OrderServiceAccommodation $orderService */
        foreach ($order->getServices()->getService() as $orderService) {
            if ($orderService->id == $externalServiceId) {
                $this->changeData($changeData, $changes, $orderService, $order);
            }
        }
    }

    private function changeData(array &$changeData, array $changes, OrderServiceAccommodation $orderService, Order $order): void
    {
        foreach ($changes as $change) {
            switch ($change) {
                case 'guests':
                    /** @var Guest $guest */
                    foreach ($orderService->getGuests()->getGuest() as $guest) {
                        $changeData[] = 'Гость ' . $guest->getFirstName() . ' ' . $guest->getLastName();
                    }
                    /** @var Child $child */
                    foreach ($orderService->getGuests()->getChildren() as $child) {
                        $changeData[] = 'Дети в возрасте ' . $child->getAge() . ' ' . $child->getCount() . ' шт';
                    }
                    break;
                case 'meals':
                    /** @var AvailableMeal $meal */
                    foreach ($orderService->getMeals()->getMeal() as $meal) {
                        $changeData[] = $this->bronevik->getMeal($meal->id).' '
                            .($meal->included?'включён':'не включён').' '
                            .'НДС:'.$meal->VATPercent.' '
                            .'Стоимость: '.$meal->priceDetails->clientCurrency->gross->price.' '
                            .'Размер НДС в стоимости: '.$meal->priceDetails->clientCurrency->gross->vatAmount.' '
                            .'Валюта: '.$meal->priceDetails->clientCurrency->gross->currency;
                    }
                    break;
                case 'priceDetails':
                    // not use
                    break;
                case 'penaltyPriceDetails':
                case 'penaltyDetails':
                    /** @var CancellationPolicy $cancellationPolicy */
                    foreach ($orderService->getCancellationPolicies() as $cancellationPolicy) {
                        $changeData[] = 'Дата и время наступления платной аннуляции:'.$cancellationPolicy->penaltyDateTime.' '
                            .'НДС '.($cancellationPolicy->penaltyPriceDetails->vatIncluded?'включён':'не включён').' '
                            .'Размер НДС в стоимости (комиссия) '.$cancellationPolicy->penaltyPriceDetails->clientCurrency->commission->vatAmount.' '
                            .'Стоимость (комиссия) '.$cancellationPolicy->penaltyPriceDetails->clientCurrency->commission->price.' '
                            .'Размер НДС в стоимости '.$cancellationPolicy->penaltyPriceDetails->clientCurrency->gross->vatAmount.' '
                            .'Стоимость '.$cancellationPolicy->penaltyPriceDetails->clientCurrency->gross->price.' '
                            .'Валюта '.$cancellationPolicy->penaltyPriceDetails->clientCurrency->gross->currency;
                    }
                    break;
                case 'hotelId':
                    $changeData[] = 'Отель '
                        .$orderService->hotelId.' '
                        .$orderService->hotelName;
                    break;
                case 'roomId':
                    $changeData[] = 'Идентификатор номера '
                        .$orderService->roomId;
                    break;
                case 'checkin':
                    $changeData[] = 'Дата заезда '
                        .$orderService->checkin;
                    break;
                case 'checkout':
                    $changeData[] = 'Дата выезда '
                        .$orderService->checkout;
                    break;
                case 'roomType':
                    $changeData[] = 'Тип размещения '
                        .RoomTypeEnum::tryFrom($orderService->roomType)->translateValue();
                    break;
                case 'guaranteeType':
                    $changeData[] = 'Тип размещения '
                        .$orderService->guaranteeType;
                    break;
                case 'paymentRecipient':
                    $changeData[] = 'Способ оплаты '
                        .($orderService->paymentRecipient == 'agency'?'безналичный':'в отеле');
                    break;
                case 'serviceStatus':
                    $changeData[] = 'Статус '
                        .StatusOrderEnum::tryFrom($orderService->statusId)->translateValue();
                    break;
                case 'serviceComment':
                    $changeData[] = 'Новый комментарий к услуге '
                        .$orderService->comment;
                    break;
                case 'serviceMessageId':
                    $changeData[] = 'Новое сообщение в чате';
                    break;
                case 'orderComment':
                    $changeData[] = 'Новый комментарий к заказу '
                        .$order->comment;
                    break;
                case 'referenceId':
                    $changeData[] = 'Новый идентификатор услуги в системе клиента '
                        .$orderService->referenceId;
                    break;
            }
        }
    }

    private function checkSha(array $header, array $data): bool
    {
        $signature = $header['HTTP_SIGNATURE_SHA256'];
        $timestamp = $data['event']['timestamp'];
        $orderId = $data['data']['orderId'];
        $serviceId = $data['data']['serviceId'];
        $clientKey = COption::GetOptionString( 'addobjectbronevik', 'key', 'naturalist_test');

        if (hash_hmac('sha256', $timestamp.'|'.$orderId.'|'.$serviceId, $clientKey) === $signature) {
            return true;
        }

        return false;
    }
}