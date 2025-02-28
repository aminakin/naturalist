<?php

namespace Local\AddObjectBronevik\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Request;
use Bitrix\Main\SystemException;
use Local\AddObjectBronevik\Lib\HotelService;
use Bitrix\Main\Engine\Controller;

class Hotel extends Controller
{
    private HotelService $service;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        $this->service = new HotelService();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function initAction($expType = false, $hotelIds = []): array
    {
        return $this->service->init($this->request, $expType, $hotelIds);
    }

    /**
     * @throws \SoapFault
     */
    public function storeAction(): ?array
    {
        return $this->service->store();
    }

    public function cancelAction(): array
    {
        return $this->service->cancel();
    }

    public function finalizeAction(): array
    {
        return $this->service->finalize();
    }
}
