<?php

namespace Local\AddObjectBronevik\Lib;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Request;
use Bitrix\Main\SystemException;
use Local\AddObjectBronevik\Orm\AddObjectBronevikTable;
use Naturalist\bronevik\ImportHotelsBronevik;
use Naturalist\bronevik\ImportHotelsMinPriceBronevik;
use SoapFault;
use COption;

class HotelService
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function init(HttpRequest|Request $request, $expType = false, $hotelIds = []): array
    {
        $arRequest = $request->toArray();
        
        $hotelIds = $this->getHotelIds($expType, $hotelIds);

        COption::SetOptionString('addobjectbronevik', 'importHotelIds', json_encode($hotelIds));
        COption::SetOptionString('addobjectbronevik', 'importHotelIndex', 0);

        COption::SetOptionString('addobjectbronevik', 'importLoadHotels', isset($arRequest['loadHotels']));
        COption::SetOptionString('addobjectbronevik', 'importLoadHotelRooms', isset($arRequest['loadHotelRooms']));
        COption::SetOptionString('addobjectbronevik', 'importLoadHotelMinimalPrice', isset($arRequest['loadHotelsMinimalPrice']));

        return [
            "PROCESSED_ITEMS" => 0,
            "TOTAL_ITEMS" => count($hotelIds),
            "STATUS" => "COMPLETED",
            "SUMMARY" => "Подготовка данных. " . count($hotelIds) . " отеля для загрузки.",
        ];
    }

    /**
     * @throws SoapFault
     */
    public function store(): array
    {
        $ids = COption::GetOptionString('addobjectbronevik', 'importHotelIds');
        $index = COption::GetOptionString('addobjectbronevik', 'importHotelIndex', 0);
        $isLoadHotels = COption::GetOptionString('addobjectbronevik', 'importLoadHotels') === '1';
        $isLoadHotelRooms = COption::GetOptionString('addobjectbronevik', 'importLoadHotelRooms') === '1';
        $isLoadHotelMinimalPrice = COption::GetOptionString('addobjectbronevik', 'importLoadHotelMinimalPrice') === '1';
        $ids = json_decode($ids);

        if ($index < count($ids)){
            $currentId = $ids[$index];

            $importHotelsBronevik = new ImportHotelsBronevik();
            $importHotelsBronevik->setAttempt(5);

            $element = AddObjectBronevikTable::getById($currentId)->fetch();
            $externalId = $element['CODE'];
            $siteHotelIds = $importHotelsBronevik($externalId, $isLoadHotels, $isLoadHotelRooms);

            if ($isLoadHotelMinimalPrice && count($siteHotelIds)) {
                $importHotelsMinPriceBronevik = new ImportHotelsMinPriceBronevik();
                $importHotelsMinPriceBronevik->setAttempt(5);
                $importHotelsMinPriceBronevik($siteHotelIds, false);
            }

            $index++;
            COption::SetOptionString('addobjectbronevik', 'importHotelIndex', $index);

            return [
                "PROCESSED_ITEMS" => $index,
                "TOTAL_ITEMS" => count($ids),
                "STATUS" => "PROGRESS",
                "SUMMARY" => "Обработано " . $index . " из " . count($ids) . " штук<br/>
                    Дождитесь завершения процесса
                    <br/><span style='color: red;'>НЕЛЬЗЯ ОДНОВРЕМЕННО ЗАПУСКАТЬ БОЛЕЕ ОДНОГО ОБРАБОТЧИКА</span>"
            ];
        }

        return [
            "PROCESSED_ITEMS" => count($ids),
            "TOTAL_ITEMS" => count($ids),
            "STATUS" => "COMPLETED",
            "SUMMARY" => "Загрузка отелей завершена."
        ];
    }

    public function cancel(): array
    {
        return [
            'STATUS' => 'COMPLETED',
        ];
    }

    public function finalize(): array
    {
        return [
            'STATUS' => 'COMPLETED',
        ];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function getHotelIds($expType = false, $hotelIds = []): array
    {
        if ($expType == 'true') {
            $allIds = AddObjectBronevikTable::getList([
                'select' => ['ID'],
            ])->fetchAll();
            $hotelIds = array_column($allIds, 'ID');
        }
        
        return $hotelIds;
    }
}