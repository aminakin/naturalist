<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\HotelRoom;
use CFile;
use CUtil;
use CIBlockElement;
use Naturalist\Products;

class ImportHotelRoomsBronevik
{
    const EXTERNAL_SERVICE = 24;

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

        $arExistElement = $this->hotelRoomBronevik->list(array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_EXTERNAL_ID" => $id, "PROPERTY_EXTERNAL_SERVICE" => self::EXTERNAL_SERVICE), false);
        if (count($arExistElement)) {
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
                "EXTERNAL_SERVICE" => self::EXTERNAL_SERVICE,
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
        foreach ($arImagesUrl as $key => $arImage) {
            $arFile = CFile::MakeFileArray($arImage["url"]);

            $pathParts = pathinfo($arFile['name']);
            if (empty($pathParts['extension'])) {
                $imageTypeArray = array
                (
                    0=>'UNKNOWN',
                    1=>'gif',
                    2=>'jpeg',
                    3=>'png',
                    4=>'swf',
                    5=>'psd',
                    6=>'bmp',
                    7=>'tiff_ii',
                    8=>'tiff_mm',
                    9=>'jpc',
                    10=>'jp2',
                    11=>'jpx',
                    12=>'jb2',
                    13=>'swc',
                    14=>'iff',
                    15=>'wbmp',
                    16=>'xbm',
                    17=>'ico',
                    18=>'count'
                );
                $size = getimagesize($arFile['tmp_name']);
                $arFile['name'] .= '.'.$imageTypeArray[$size[2]];
            }

            if ($arFile) {
                $arImages[] = $arFile;
            }
        }

        return $arImages;
    }
}