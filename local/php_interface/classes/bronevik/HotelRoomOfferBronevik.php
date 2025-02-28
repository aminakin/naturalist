<?php

namespace Naturalist\bronevik;

use CIBlockElement;
class HotelRoomOfferBronevik
{
    public function stage(array $data)
    {
        $iE = new CIBlockElement();
        $iE->Add($data);
    }

    public function update(int $id, array $data)
    {
        $iE = new CIBlockElement();
        $iE->Update($id, $data);
    }
}