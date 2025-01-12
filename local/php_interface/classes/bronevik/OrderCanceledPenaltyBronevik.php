<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\CancellationPolicy;
use Bronevik\HotelsConnector\Element\Order;
use Bronevik\HotelsConnector\Element\OrderServices;

class OrderCanceledPenaltyBronevik
{
    private OrderBronevik $orderBronevik;

    public function __construct()
    {
        $this->orderBronevik = new OrderBronevik();
    }

    public function __invoke($orderId)
    {
        $date = time();
        $penalties = 0;

        /** @var Order $order */
        $order = $this->orderBronevik->show($orderId);

        /** @var OrderServices $orderService */
        foreach ($order->getServices()->getService() as $orderService) {
            $lastDate = 0;
            $penalty = 0;

            /** @var CancellationPolicy $cancellationPolicy */
            foreach ($orderService->getCancellationPolicies() as $cancellationPolicy) {
                if ($lastDate < strtotime($cancellationPolicy->penaltyDateTime) && $date > strtotime($cancellationPolicy->penaltyDateTime)) {
                    $lastDate = strtotime($cancellationPolicy->penaltyDateTime);
                    $penalty = $cancellationPolicy->penaltyPriceDetails->clientCurrency->gross->price;
                }
            }

            $penalties += $penalty;
        }

        return $penalties;
    }
}