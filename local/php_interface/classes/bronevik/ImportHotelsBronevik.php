<?php

namespace Naturalist\bronevik;

use Bronevik\HotelsConnector\Element\Hotel;
use CFile;
use CIBlockSection;
use Naturalist\bronevik\repository\Bronevik;

class ImportHotelsBronevik
{
    use AttemptBronevik;

    private Bronevik $bronevik;

    private ImportHotelRoomsBronevik $importnerHotelRoomsBronevik;

    private HotelBronevik $hotelBronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
        $this->importnerHotelRoomsBronevik = new ImportHotelRoomsBronevik();
        $this->hotelBronevik = new HotelBronevik();
    }

    /**
     * @throws \SoapFault
     */
    public function __invoke(int | array $ids, bool $isLoadHotels = true, bool $isLoadHotelRooms = true): array
    {
        $siteHotelIds = [];
        if (gettype($ids) === 'integer') {
            $ids = [$ids];
        }

        $hotels = $this->bronevik->getHotelInfo($ids);
        foreach ($hotels->hotel as $hotel) {
            $data = $this->getHotelData($hotel);
            $id = null;
            if ($isLoadHotels && $id = $this->upsert($data)) {
                $siteHotelIds[] = $id;
            }

            if (! $isLoadHotels) {
                $id = $this->getId($data);
                $siteHotelIds[] = $id;
            }

            if ($isLoadHotelRooms && gettype($id) == 'integer') {
                ($this->importnerHotelRoomsBronevik)($id, $hotel?->rooms?->room);
            }

        }

        return $siteHotelIds;
    }

    private function getHotelData(Hotel $data): array
    {
        $arFields = [];

        $sectionCode = \CUtil::translit($data->name, "ru");
        $arFields["IBLOCK_ID"] = CATALOG_IBLOCK_ID;
        $arFields["UF_EXTERNAL_ID"] = $data->id;
        $arFields["UF_INFORMATIONS"] = json_encode($data?->informationForGuest?->notification);
        $arFields["UF_TAXES"] = $data->hasTaxes ? json_encode($data?->taxes?->tax) : '';
        $arFields["UF_ADDITIONAL_INFO"] = json_encode($data?->additionalInfo);
        $arFields["UF_ALLOWABLE_TIME"] = json_encode(['allowableCheckinTime' => $data?->allowableCheckinTime, 'allowableCheckoutTime' => $data?->allowableCheckoutTime]);
        $arFields["UF_EXTERNAL_SERVICE"] = CATALOG_IBLOCK_SECTION_UF_EXTERNAL_SERVICE_ID;
        $arFields["UF_ADDRESS"] = $data->cityName . '. ' . $data->address;
        $arFields["UF_TIME_FROM"] = $data->checkinTime;
        $arFields["UF_TIME_TO"] = $data->checkoutTime;
        $arFields["UF_PHOTO_ARRAY"] = json_encode($this->getImagesAsArray($data->descriptionDetails->photos->photo));
        $arFields["ACTIVE"] = "N";
        $arFields["NAME"] = $data->name;
        $arFields["CODE"] = $sectionCode;
        $arFields["DESCRIPTION"] = $data->descriptionDetails->description;
        $arFields["UF_COORDS"] = $data->descriptionDetails->latitude . ',' . $data->descriptionDetails->longitude;

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

    private function getId(array $data): ?int
    {
        $arSection = $this->hotelBronevik->listFetch(["UF_EXTERNAL_ID" => $data["UF_EXTERNAL_ID"]], false, ["IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"]);
        if (count($arSection)) {
            $arSection = current($arSection);

            return $arSection['ID'];
        }

        return null;
    }

    private function upsert(array $data): int|bool
    {
        $arSection = $this->hotelBronevik->listFetch(["UF_EXTERNAL_ID" => $data["UF_EXTERNAL_ID"]], false, ["IBLOCK_ID", "ID", "NAME", "CODE", "SORT", "UF_*"]);
        $iS = new CIBlockSection();
        if (count($arSection)) {
            $arSection = current($arSection);
            $isLoadImage = true;

            if ($arSection) {
                $isLoadImage = $arSection["UF_CHECKBOX_1"] != 1;
            }

            $photos = $this->downloadSectionImages($data, $isLoadImage);
            if (is_array($photos)) {
                $data['UF_PHOTOS'] = $photos;
            }

            $iS->Update($arSection['ID'], array_filter($data, function ($k) { return in_array($k, ['UF_TAXES', 'UF_INFORMATIONS', 'UF_ADDITIONAL_INFO', 'UF_TIME_FROM', 'UF_TIME_TO', 'UF_ALLOWABLE_TIME']); }, ARRAY_FILTER_USE_KEY));
            if (true) {
                return $arSection['ID'];
            }
        } else {
            return $iS->Add($data);
        }
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