<?php

namespace Naturalist\bronevik;

use CIBlockSection;
use CFile;

class ImportHotelsBronevik
{
    const EXTERNAL_SERVICE_ID = 6;

    private Bronevik $bronevik;

    private ImportHotelRoomsBronevik $importnerHotelRoomsBronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
        $this->importnerHotelRoomsBronevik = new ImportHotelRoomsBronevik();
    }

    /**
     * @throws \SoapFault
     */
    public function __invoke(int | array $ids): void
    {
        if (gettype($ids) === 'integer') {
            $ids = [$ids];
        }

        $hotels = $this->bronevik->getHotelInfo($ids);
        foreach ($hotels->hotel as $hotel) {
            //var_export($hotel?->rooms?->room); die();
            $data = $this->getHotelData($hotel);
            if ($id = $this->upsert($data))
            {
                ($this->importnerHotelRoomsBronevik)($id, $hotel?->rooms?->room);
            }
        }

        //var_export($data); die();
    }

    private function getHotelData($data): array
    {
        $arFields = [];

        $sectionCode = \CUtil::translit($data->name, "ru");
        $arFields["IBLOCK_ID"] = CATALOG_IBLOCK_ID;
        $arFields["UF_EXTERNAL_ID"] = $data->id;
        $arFields["UF_EXTERNAL_SERVICE"] = self::EXTERNAL_SERVICE_ID;
        $arFields["UF_ADDRESS"] = $data->cityName . '. ' . $data->address;
        $arFields["UF_TIME_FROM"] = $data->checkinTime;
        $arFields["UF_TIME_TO"] = $data->checkoutTime;
        $arFields["UF_PHOTO_ARRAY"] = json_encode($this->getImagesAsArray($data->descriptionDetails->photos->photo));
        $arFields["ACTIVE"] = "N";
        $arFields["NAME"] = $data->name;
        $arFields["CODE"] = $sectionCode;
        $arFields["DESCRIPTION"] = $data->descriptionDetails->description;
        $arFields["UF_COORDS"] = $data->descriptionDetails->latitude . ',' . $data->descriptionDetails->longitude;

        /*$data->descriptionDetails->type == 'hotel';
        $data->descriptionDetails->category == 3;
        $data->descriptionDetails->rooms->room;*/

        return $arFields;
    }

    private function getImagesAsArray(array $images): array
    {
        $result = [];
        foreach ($images as $image) {
            $result[] = ['url' => $image->url];
        }

        return $result;
    }

    private function upsert(array $data): int|null|bool
    {
        // TODO move to HotelBronevik
        $arSection = CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => CATALOG_IBLOCK_ID,
                "UF_EXTERNAL_ID" => $data["UF_EXTERNAL_ID"]
            ),
            false,
            array("IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"),
            false
        )->Fetch();

        $isLoadImage = true;

        if ($arSection) {
            $isLoadImage = $arSection["UF_CHECKBOX_1"] != 1;
        }

        $photos = $this->downloadSectionImages($data, $isLoadImage);
        if (is_array($photos)) {
            $data['UF_PHOTOS'] = $photos;
        }
        // TODO move to HotelBronevik
        $iS = new CIBlockSection();
        if ($arSection) {
            if ($iS->Update($arSection['ID'], $data)) {
                return $arSection['ID'];
            }
        } else {
            return $iS->Add($data);
        }

        return null;
    }

    private static function getImages($arImagesUrl)
    {
        $arImages = array();
        foreach ($arImagesUrl as $key => $arImage) {
            $arFile = CFile::MakeFileArray($arImage["url"]);

            if ($arFile) {
                $arImages[] = $arFile;
            }
        }

        return $arImages;
    }

    private function downloadSectionImages(array $arSectionData, bool $isLoadImage): ?array
    {
        if ($isLoadImage) {
            $arImages = self::getImages(json_decode($arSectionData["UF_PHOTO_ARRAY"], true));
            if (count($arImages)) {
                return $arImages;
                $arFields["UF_PHOTOS"] = $arImages;
                // TODO move to HotelBronevik
                $iS = new CIBlockSection();
                $res = $iS->Update($arSection['ID'], $arFields);

                if ($res) {
                    echo date('Y-M-d H:i:s') . " Загружены фото для раздела (" . $section['ID'] . ") \"" . $section['NAME'] . "\"<br>\r\n";
                }
            }
        }

        return null;
    }
}