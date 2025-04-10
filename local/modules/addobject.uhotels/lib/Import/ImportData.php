<?php

namespace Addobject\Uhotels\Import;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Diag\Debug;
use Addobject\Uhotels\Connector\UhotelsConnector;
use Bitrix\Main\Loader;
use CIBlockElement;
use CIBlockSection;
use CUtil;
use Exception;
use UHotels\ApiClient\Dto\Hotel\HotelDto;
use UHotels\ApiClient\Dto\Room\RoomDto;

class ImportData
{
    private int $catalogIBlockID;
    private int $roomsFeaturesHLId = 8;
    private int $uhotelsSectionPropEnumId = 13;
    private int $uhotelselementPropEnumId = 39;

    public function __construct()
    {
        $this->catalogIBlockID = CATALOG_IBLOCK_ID;
    }

    /**
     * Импортирует данные из UHotels.
     *
     * @param string $uid Уникальный идентификатор объекта.
     * @param bool $onlyData Флаг возврата только данных.
     * @return array
     * @throws Exception
     */
    public function import(string $uid, bool $onlyData = false): array
    {
        $arResult = [
            "MESSAGE" => [
                "ERRORS" => [],
                "SUCCESS" => [],
            ],
        ];

        $connector = new UhotelsConnector($uid);
        $data = $connector->getHotel();

        if ($onlyData) {
            return $data;
        }

        $section = $this->findOrCreateSection($uid, $data, $arResult);

        if (!$section['SUCCESS']) {
            return $arResult;
        }
        $arResult['MESSAGE']['ERRORS'] = [];

        $this->processRooms($section['DATA'], $connector, $arResult);

        return $arResult;
    }

    private function findOrCreateSection(string $uid, HotelDto $data, array &$arResult): array
    {
        $section = $this->findSectionByUid($uid);

        if ($section) {
            $arResult['MESSAGE']['ERRORS'][] = "Объект с ID {$uid} уже существует";
            return ['SUCCESS' => true, 'DATA' => $section];
        }

        $createdSection = $this->createSection($data, $uid);

        if (!$createdSection['ID']) {
            $arResult['MESSAGE']['ERRORS'][] = "Ошибка создания раздела: " . $createdSection['LAST_ERROR'];
            return ['SUCCESS' => false];
        }

        $arResult['MESSAGE']['SUCCESS'][] = "Добавлен объект с ID {$uid}: {$createdSection['NAME']}";
        return ['SUCCESS' => true, 'DATA' => $createdSection];
    }

    private function processRooms(array $section, UhotelsConnector $connector, array &$arResult): void
    {
        $dataRooms = $connector->getRooms();
        $roomsResult = $this->updateRooms($section, $dataRooms);

        if ($roomsResult['SUCCESS']) {
            $arResult['MESSAGE']['SUCCESS'][] = "Добавлены/обновлены номера";
        } else {
            $arResult['MESSAGE']['ERRORS'][] = $roomsResult['ERROR'];
        }
    }

    private function findSectionByUid(string $uid): ?array
    {
        $section = CIBlockSection::GetList(
            ["ID" => "ASC"],
            [
                "IBLOCK_ID" => $this->catalogIBlockID,
                "UF_EXTERNAL_SERVICE" => $this->uhotelsSectionPropEnumId,
                "UF_EXTERNAL_ID" => $uid,
            ],
            false,
            ["IBLOCK_ID", "ID", "NAME", "CODE", "UF_*"]
        )->Fetch();

        return $section ?: null;
    }

    private function createSection(HotelDto $hotel, string $uid): array
    {
        $phones = array_map(fn($phone) => $phone->phone, $hotel->phones);
        $address = $hotel->address;

        $fields = [
            "IBLOCK_ID" => $this->catalogIBlockID,
            "ACTIVE" => "N",
            "NAME" => $hotel->name,
            "CODE" => CUtil::translit($hotel->name, "ru"),
            "UF_EXTERNAL_ID" => $uid,
            "UF_EXTERNAL_SERVICE" => $this->uhotelsSectionPropEnumId,
            "UF_ADDRESS" => $address->text,
            "UF_PHONE" => implode(', ', $phones),
            "UF_TIME_FROM" => $hotel->times->in,
            "UF_TIME_TO" => $hotel->times->out,
            "UF_COORDS" => implode(',', $address->coords),
        ];

        $section = new CIBlockSection();
        $sectionId = $section->Add($fields);

        return $sectionId
            ? ["ID" => $sectionId, "NAME" => $hotel->name, "UF_EXTERNAL_ID" => $uid]
            : ["LAST_ERROR" => $section->LAST_ERROR];
    }

    private function updateRooms(array $section, array $dataRooms): array
    {
        $errors = [];

        foreach ($dataRooms as $room) {
            try {
                $element = $this->findOrCreateRoomElement($section, $room);
                $this->updateRoomProperties($element['ID'], $room);
            } catch (Exception $e) {
                $errors[] = "Ошибка обработки номера {$room['id']}: " . $e->getMessage();
            }
        }

        return $errors
            ? ['SUCCESS' => false, 'ERROR' => implode('; ', $errors)]
            : ['SUCCESS' => true];
    }

    private function findOrCreateRoomElement(array $section, RoomDto $roomData): array
    {
        $existing = CIBlockElement::GetList(
            [],
            [
                "IBLOCK_ID" => $this->catalogIBlockID,
                "SECTION_ID" => $section['ID'],
                "PROPERTY_EXTERNAL_ID" => $roomData->id,
                "PROPERTY_EXTERNAL_SERVICE" => $this->uhotelselementPropEnumId
            ],
            false,
            false,
            ["ID"]
        )->Fetch();

        if ($existing) {
            return ['ID' => $existing['ID'], 'IS_NEW' => false];
        }

        $elementFields = [
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $this->catalogIBlockID,
            "IBLOCK_SECTION_ID" => $section['ID'],
            "NAME" => $roomData->name,
            "CODE" => CUtil::translit($roomData->name, "ru"),
            "DETAIL_TEXT" => $roomData->desc,
            "DETAIL_TEXT_TYPE" => 'html',
        ];

        $elementId = (new CIBlockElement())->Add($elementFields);

        if (!$elementId) {
            throw new Exception("Ошибка создания номера: " . $elementId->LAST_ERROR);
        }

        return ['ID' => $elementId, 'IS_NEW' => true];
    }

    private function updateRoomProperties(int $elementId, RoomDto $roomData): void
    {
        $properties = [
            //"PHOTOS" => $this->processRoomImages($roomData),
            "EXTERNAL_SERVICE" => $this->uhotelselementPropEnumId,
            "EXTERNAL_ID" => $roomData->id,
            "BEDS" => $roomData->places,
            "FEATURES" => $roomData->equipments_main ? $this->getRoomFeatures($roomData->equipments_main) : false,
        ];

        CIBlockElement::SetPropertyValuesEx($elementId, $this->catalogIBlockID, $properties);
    }

    private function processRoomImages(array $photos): array
    {
        // Реализация обработки изображений
        return [];
    }


    private function getRoomFeatures(array $roomData): array
    {
        if (empty($roomData)) {
            return [];
        }

        $entityClass = $this->getEntityClass($this->roomsFeaturesHLId);

        // Получаем все существующие элементы за один запрос
        $existingItems = $entityClass::getList([
            'select' => ['UF_XML_ID'],
            'filter' => ['UF_XML_ID' => $roomData],
        ])->fetchAll();

        $existingXmlIds = array_column($existingItems, 'UF_XML_ID');
        $newItems = array_diff($roomData, $existingXmlIds);

        // Массовое добавление новых элементов
        if (!empty($newItems)) {
            $addBatch = [];
            foreach ($newItems as $item) {
                $addBatch[] = [
                    'UF_NAME' => $item,
                    'UF_XML_ID' => $item,
                ];
            }
            $entityClass::addMulti($addBatch);
        }

        return array_unique(array_merge($existingXmlIds, $newItems));
    }

    private function getEntityClass($hlId = 8)
    {
        Loader::IncludeModule('highloadblock');

        $hlblock = HighloadBlockTable::getById($hlId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlblock);
        return $entity->getDataClass();
    }
}