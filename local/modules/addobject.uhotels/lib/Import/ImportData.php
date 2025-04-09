<?php

namespace Addobject\Uhotels\Import;

use Bitrix\Main\Diag\Debug;
use Addobject\Uhotels\Connector\UhotelsConnector;
use CIBlockSection;
use UHotels\ApiClient\Dto\Hotel\HotelDto;

class ImportData
{
    private static int $catalogIBlockID = CATALOG_IBLOCK_ID;
    private string $uhotelsSectionPropEnumId = '13';

    /**
     * Импортирует данные из UHotels.
     *
     * @param string $uid Уникальный идентификатор объекта.
     * @param bool $onlyRooms Флаг импорта только комнат.
     * @param bool $onlyTariffs Флаг импорта только тарифов.
     * @param bool $onlyData Флаг возврата только данных.
     * @return array
     * @throws \Exception
     */
    public function import(string $uid, bool $onlyRooms = false, bool $onlyTariffs = false, bool $onlyData = false): array
    {
        // Инициализация результата
        $arResult = [
            "MESSAGE" => [
                "ERRORS" => '',
                "SUCCESS" => '',
            ],
        ];

        // Получение данных через коннектор
        $connector = new UhotelsConnector($uid);
        $data = $connector->getHotel();

        if ($onlyData) {
            return $data;
        }

        // Поиск существующего раздела
        $section = $this->findSectionByUid($uid);

        if (empty($section)) {
            // Создание нового раздела
            $section = $this->createSection($data, $uid);
            if ($section) {
                $arResult["MESSAGE"]["SUCCESS"] = "Добавлен объект с ID {$uid}: {$section['NAME']}";
            } else {
                $arResult["MESSAGE"]["ERRORS"] = "Ошибка при создании раздела: " . $this->getLastSectionError();
            }
        } else {
            // Обновление существующего раздела
            if (is_array($data['account'])) {
                $section = array_merge($data['account'], $section);
            }
            $arResult["MESSAGE"]["ERRORS"] = "Объект с указанным ID уже существует. Данные по объекту были обновлены.";
        }

        return $arResult;
    }

    /**
     * Находит раздел по UID.
     *
     * @param string $uid Уникальный идентификатор объекта.
     * @return array|null
     */
    private function findSectionByUid(string $uid): ?array
    {
        $section = CIBlockSection::GetList(
            ["ID" => "ASC"],
            [
                "IBLOCK_ID" => self::$catalogIBlockID,
                "ACTIVE" => "Y",
                "UF_EXTERNAL_SERVICE" => $this->uhotelsSectionPropEnumId,
                "UF_EXTERNAL_UID" => $uid,
            ],
            false,
            ["IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"],
            false
        )->Fetch();

        return $section ?: null;
    }

    /**
     * Создает новый раздел.
     *
     * @param array $account Данные об отеле.
     * @param string $uid Уникальный идентификатор объекта.
     * @return array|null
     */
    private function createSection(HotelDto  $account, string $uid): ?array
    {
        $sectionName = $account->name;
        $sectionCode = \CUtil::translit($sectionName, "ru");

        $fields = [
            "IBLOCK_ID" => self::$catalogIBlockID,
            "ACTIVE" => "N",
            "NAME" => $sectionName,
            "CODE" => $sectionCode,
            "UF_EXTERNAL_ID" => $uid,
            "UF_EXTERNAL_SERVICE" => $this->uhotelsSectionPropEnumId,
            "UF_ADDRESS" => $account->address->text,
            "UF_EMAIL" => $account['email'],
            "UF_PHONE" => $account['phone'],
            "UF_TIME_FROM" => $account['checkin'],
            "UF_TIME_TO" => $account['checkout'],
            "UF_COORDS" => implode(',', $account->address->coords),
        ];

        $section = new CIBlockSection();
        $sectionId = $section->Add($fields);

        if ($sectionId) {
            return [
                "ID" => $sectionId,
                "NAME" => $sectionName,
                "UF_EXTERNAL_ID" => $account["id"],
            ];
        }

        return null;
    }

    /**
     * Возвращает последнюю ошибку создания раздела.
     *
     * @return string
     */
    private function getLastSectionError(): string
    {
        global $APPLICATION;
        return $APPLICATION->GetException()->GetString() ?? "Неизвестная ошибка";
    }
}