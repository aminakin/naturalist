<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\Order;
use Naturalist\bronevik\repository\Bronevik;
use SoapFault;

class OrderBronevik
{
    private Bronevik $bronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
    }

    /**
     * @throws SoapFault
     */
    public function show(int $orderId): Order
    {
        return $this->bronevik->getOrder($orderId);
    }
}