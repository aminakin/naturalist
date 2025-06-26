<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\HotelRoom;
use CFile;
use CUtil;
use CIBlockElement;
use Naturalist\Products;

class ImportHotelRoomsBronevik
{
    private HotelRoomBronevik $hotelRoomBronevik;


    public function __construct()
    {
        $this->hotelRoomBronevik = new HotelRoomBronevik();
    }
    public function __invoke(int $hotelId, array $rooms): void
    {
        foreach ($rooms as $room) {
            $data = $this->getRoomData($hotelId, $room);
            $this->upsert($data);
        }
    }

    private function upsert(array $data): int
    {
        $id = $data['PROPERTY_VALUES']['EXTERNAL_ID'];

        $arExistElement = $this->hotelRoomBronevik->list(
            ["IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_EXTERNAL_ID" => $id, "PROPERTY_EXTERNAL_SERVICE" => CATALOG_IBLOCK_ELEMENT_EXTERNAL_SERVICE_ID],
            false,
            ['ID']);

        if (count($arExistElement) && intval(current($arExistElement)['ID']) > 0) {
            $itemExistElement = current($arExistElement);
            $itemId = $itemExistElement['ID'];
            $this->hotelRoomBronevik->update($itemId, ['CODE' => $data['CODE']]);

            CIBlockElement::SetPropertyValuesEx($arExistElement['ID'], CATALOG_IBLOCK_ID, [
                'SQUARE' => $data['PROPERTY_VALUES']['SQUARE'],
                'PHOTO_ARRAY' => $data['PROPERTY_VALUES']['PHOTO_ARRAY'],
                'PHOTOS' => $data['PROPERTY_VALUES']['PHOTOS'],
            ]);
            Products::setQuantity($arExistElement['ID']);
            Products::setPrice($arExistElement['ID']);

            return $itemId;
        } else {
            $itemId = $this->hotelRoomBronevik->store($data);
            Products::setQuantity($itemId);
            Products::setPrice($itemId);

            return $itemId;
        }
    }

    private function getRoomData($hotelId, HotelRoom $room): array
    {
        $elementCode = CUtil::translit($room->name, "ru");
        $arrayPhotos = $this->getImagesAsArray($room->photos->photo);

        return [
            "ACTIVE" => "Y",
            "IBLOCK_ID" => CATALOG_IBLOCK_ID,
            "IBLOCK_SECTION_ID" => $hotelId,
            "NAME" => $room->name,
            "CODE" => $elementCode,
            "DETAIL_TEXT" => $room->description,
            "DETAIL_TEXT_TYPE" => 'html',
            "PROPERTY_VALUES" => array(
                "PHOTO_ARRAY" => json_encode($arrayPhotos),
                "PHOTOS" => self::getImages($arrayPhotos),
                "EXTERNAL_ID" => $room->id,
                "EXTERNAL_SERVICE" => CATALOG_IBLOCK_ELEMENT_EXTERNAL_SERVICE_ID,
                "SQUARE" => $room->size,
            ),
        ];
    }

    private function getImagesAsArray(array $images): array
    {
        $result = [];
        foreach ($images as $image) {
            $result[] = ['url' => $image->url];
        }

        return $result;
    }

    private static function getImages($arImagesUrl): array
    {
        $arImages = [];
        foreach ($arImagesUrl as $arImage) {
            $originalUrl = $arImage['url'];

            // Варианты URL по приоритету (от лучшего к худшему)
            $urlOptions = [
                str_replace('/250x250a/', '/', $originalUrl),        // 1440x1080 - полный размер
                $originalUrl                                          // 250x250 - маленький размер
            ];

            $fileId = null;
            foreach ($urlOptions as $testUrl) {
                $fileId = self::downloadAndSaveImageManually($testUrl);
                if ($fileId) {
                    error_log("Успешно загружено изображение: $testUrl");
                    break; // Прерываем при успешном сохранении
                }
            }

            if ($fileId) {
                $arImages[] = $fileId;
            } else {
                error_log("Не удалось загрузить ни один вариант для: $originalUrl");
            }
        }

        return $arImages;
    }

    private static function downloadAndSaveImageManually(string $url): ?int
    {
        // Создаем контекст для загрузки
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'timeout' => 60,
                'follow_location' => true,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        // Загружаем изображение
        $imageData = @file_get_contents($url, false, $context);
        if (!$imageData) {
            error_log("Не удалось загрузить: $url");
            return null;
        }

        // 1. СОХРАНИТЬ ФАЙЛ ПРИНУДИТЕЛЬНО
        $tempFile = tempnam(sys_get_temp_dir(), 'bronevik_');
        file_put_contents($tempFile, $imageData);
        unset($imageData); // Освобождаем память

        // Проверяем что это изображение
        $imageInfo = getimagesize($tempFile);
        if (!$imageInfo) {
            @unlink($tempFile);
            error_log("Файл не является изображением: $url");
            return null;
        }

        // Создаем принудительно сохраненный файл
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $ext = $extensions[$imageInfo['mime']] ?? 'jpg';
        $filename = uniqid('img_') . '.' . $ext;

        $forceDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/temp_bronevik/';
        if (!file_exists($forceDir)) {
            mkdir($forceDir, 0755, true);
        }

        $forcePath = $forceDir . $filename;
        if (!copy($tempFile, $forcePath)) {
            @unlink($tempFile);
            error_log("Не удалось создать принудительный файл: $forcePath");
            return null;
        }
        chmod($forcePath, 0644);

        // 2. ПРОВЕРИТЬ ЧТО ОН СУЩЕСТВУЕТ
        if (!file_exists($forcePath) || filesize($forcePath) === 0) {
            @unlink($tempFile);
            error_log("Принудительно созданный файл не существует или пуст: $forcePath");
            return null;
        }
        error_log("Принудительно создан файл: $forcePath, размер: " . filesize($forcePath) . " байт");

        // 3. СОХРАНИТЬ ФАЙЛ В БИТРИКС
        $arFile = [
            'name' => $filename,
            'type' => $imageInfo['mime'],
            'tmp_name' => $tempFile,
            'size' => filesize($tempFile),
            'error' => 0,
        ];

        $fileId = CFile::SaveFile($arFile, 'bronevik');

        if (!$fileId) {
            error_log("CFile::SaveFile вернул false для: $url");
            @unlink($tempFile);
            @unlink($forcePath);
            return null;
        }

        // 4. ПРОВЕРИТЬ ЕГО СУЩЕСТВОВАНИЕ В БИТРИКС
        $savedFileInfo = CFile::GetFileArray($fileId);
        if (!$savedFileInfo) {
            error_log("Файл с ID $fileId не найден в БД");
            @unlink($tempFile);
            @unlink($forcePath);
            return null;
        }

        $bitrixFilePath = $_SERVER['DOCUMENT_ROOT'] . $savedFileInfo['SRC'];
        error_log("Битрикс должен создать файл: $bitrixFilePath");

        // 5. ЕСЛИ ФИЗИЧЕСКИ НЕ СУЩЕСТВУЕТ, СОЗДАТЬ ПО ПУТИ ИЗ БАЗЫ
        if (!file_exists($bitrixFilePath)) {
            error_log("Физический файл Битрикс не создан, создаем принудительно: $bitrixFilePath");

            // Создаем директорию если нужно
            $bitrixDir = dirname($bitrixFilePath);
            if (!file_exists($bitrixDir)) {
                if (!mkdir($bitrixDir, 0755, true)) {
                    error_log("Не удалось создать директорию Битрикс: $bitrixDir");
                    CFile::Delete($fileId);
                    @unlink($tempFile);
                    @unlink($forcePath);
                    return null;
                }
                error_log("Создана директория Битрикс: $bitrixDir");
            }

            // Копируем принудительно созданный файл в место Битрикс
            if (!copy($forcePath, $bitrixFilePath)) {
                error_log("Не удалось скопировать файл в Битрикс: $forcePath -> $bitrixFilePath");
                CFile::Delete($fileId);
                @unlink($tempFile);
                @unlink($forcePath);
                return null;
            }

            chmod($bitrixFilePath, 0644);
            error_log("Файл принудительно скопирован в Битрикс: $bitrixFilePath");
        } else {
            error_log("Файл Битрикс создан автоматически: $bitrixFilePath");
        }

        // Проверяем финальное существование файла
        $finalSize = filesize($bitrixFilePath);
        if ($finalSize === 0) {
            error_log("Финальный файл пуст: $bitrixFilePath");
            CFile::Delete($fileId);
            @unlink($tempFile);
            @unlink($forcePath);
            return null;
        }

        // 6. УДАЛИТЬ ПРИНУДИТЕЛЬНО СКАЧАННЫЙ ФАЙЛ
        @unlink($tempFile);
        @unlink($forcePath);
        error_log("Временные файлы удалены");

        error_log("Файл успешно сохранен: {$imageInfo[0]}x{$imageInfo[1]}, размер: " . round($finalSize/1024/1024, 2) . " MB, ID: $fileId, путь: {$savedFileInfo['SRC']}");

        return $fileId;
    }



}