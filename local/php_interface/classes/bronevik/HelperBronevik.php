<?php

namespace Naturalist\bronevik;

use CIBlockElement;
class HelperBronevik
{
    public function dsd(int $id, int $iBlockId, array $properties)
    {
        CIBlockElement::SetPropertyValuesEx($id, $iBlockId, $properties);
    }
}