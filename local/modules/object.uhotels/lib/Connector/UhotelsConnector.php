<?php

namespace Object\Uhotels\Connector;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use UHotels\ApiClient\Client;
use UHotels\ApiClient\Dto\Booking\BookingDto;
use UHotels\ApiClient\Dto\Quota\QuotaDto;
use UHotels\ApiClient\Dto\Room\RoomDto;
use UHotels\ApiClient\Dto\Tariff\TariffDto;
use UHotels\ApiClient\Service\BookingService;
use UHotels\ApiClient\Service\HotelService;
use UHotels\ApiClient\Service\OccupancyService;
use UHotels\ApiClient\Service\QuotaService;
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
    private QuotaService $quotaService;
    private OccupancyService $occupancyService;
    private BookingService $bookingService;

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
        $this->quotaService = new QuotaService($client);
        $this->occupancyService = new OccupancyService($client);
        $this->bookingService = new BookingService($client);
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
     * @throws GuzzleException
     */
    public function getRooms()
    {
        return $this->roomService->getList();
    }

    /**
     * Получить информацию о тарифах.
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function getTariffList(): array
    {
        return $this->tariffService->getList();
    }

    /**
     * Получить информацию о тарифах.
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function getTariffById($tariffId): TariffDto
    {
        return $this->tariffService->get($tariffId);
    }

    /**
     * Получение списка квот
     *
     * @throws GuzzleException
     */
    public function getQuota(?string $dateStart = null, ?string $dateFinish = null, ?int $roomId = null): array
    {
        return $this->quotaService->getList($dateStart, $dateFinish, $roomId);
    }

    /**
     * Получение информации по загрузке номеров
     *
     * @param string|null $dateStart
     * @param string|null $dateFinish
     * @param int|null $roomId
     * @param int|null $tariffId
     * @return array
     * @throws GuzzleException
     */
    public function getOccupancy(?string $dateStart = null, ?string $dateFinish = null, ?int $roomId = null, ?int $tariffId = null)
    {
        return $this->occupancyService->getList($dateStart, $dateFinish, $roomId, $tariffId);
    }

    /**
     *  Метод регистрации бронирования
     *
     * @param $bookingCreateData
     * @return BookingDto
     * @throws GuzzleException
     */
    public function addBooking($bookingCreateData): BookingDto
    {
        return $this->bookingService->create($bookingCreateData);
    }

    /**
     * Отмена бронирования
     *
     * @param $bookingId
     * @return BookingDto
     * @throws GuzzleException
     */
    public function cancelBooking($bookingId): BookingDto
    {
        return $this->bookingService->cancel($bookingId);
    }

    /**
     * Получение информации о бронировании по номеру
     *
     * @param $bookingId
     * @return BookingDto
     * @throws GuzzleException
     */
    public function getBookingById($bookingId): BookingDto
    {
        return $this->bookingService->get($bookingId);
    }

    /**
     * Получение списка бронирований
     * @return array
     * @throws GuzzleException
     */
    public function getBookingList(): array
    {
        return $this->bookingService->getList();
    }
}