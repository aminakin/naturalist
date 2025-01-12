<?php

namespace Naturalist\bronevik;

use Naturalist\bronevik\repository\Bronevik;

class OrderCancelBronevik
{
    private Bronevik $bronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
    }

    public function __invoke(string $orderId)
    {
        return $this->bronevik->CancelOrder($orderId);
    }
}