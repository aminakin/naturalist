<?php

namespace Addobject\Uhotels\Connector;

use Exception;
use UHotels\ApiClient\Client;
use UHotels\ApiClient\Service\HotelService;
use UHotels\ApiClient\Service\RoomService;
use UHotels\ApiClient\Service\TariffService;

/**
 * @url https://gitlab.addamant-work.ru/packages/uhotels-api
 */
class UhotelsConnector
{
    private HotelService $hotelService;
    private RoomService $roomService;
    private TariffService $tariffService;

    /**
     * Конструктор класса.
     *
     * @param string $token Токен для аутентификации в API.
     *
     * @throws Exception Если токен не передан.
     */
    public function __construct(string $token)
    {
        if (empty($token)) {
            throw new Exception('API token is required for UHotelsConnector.');
        }

        $client = new Client('https://account2.uhotels.app/api/', $token);

        // Инициализация сервисов
        $this->hotelService = new HotelService($client);
        $this->roomService = new RoomService($client);
        $this->tariffService = new TariffService($client);
    }

    /**
     * Получить информацию об отелях.
     *
     * @return mixed
     */
    public function getHotel()
    {
        return $this->hotelService->get();
    }

    /**
     * Получить список комнат.
     *
     * @return mixed
     */
    public function getRooms()
    {
        return $this->roomService->getList();
    }

    /**
     * Получить информацию о тарифах.
     *
     * @return mixed
     */
    public function getTariffs()
    {
        return $this->tariffService->get();
    }
}