<?php

namespace Naturalist\bronevik;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bronevik\HotelsConnector\Element\AvailableMeal;
use Bronevik\HotelsConnector\Element\Child;
use Bronevik\HotelsConnector\Element\HotelOffer;
use Bronevik\HotelsConnector\Element\HotelOfferCancellationPolicy;
use Bronevik\HotelsConnector\Element\Hotels;
use Bronevik\HotelsConnector\Element\HotelWithOffers;
use Bronevik\HotelsConnector\Element\OfferPolicy;
use Bronevik\HotelsConnector\Element\RateType;
use Bronevik\HotelsConnector\Element\SearchOfferCriterionNumberOfGuests;
use Bronevik\HotelsConnector\Element\SearchOfferCriterionOnlyOnline;
use Bronevik\HotelsConnector\Element\SearchOfferCriterionPaymentRecipient;
use Bronevik\HotelsConnector\Element\SkipElements;
use Bronevik\HotelsConnector\Enum\CurrencyCodes;
use CIBlockElement;
use Naturalist\bronevik\repository\Bronevik;

class SearchRoomsBronevik
{
    private Bronevik $bronevik;

    private HotelRoomBronevik $hotelRoomBronevi;
    private HotelRoomOfferBronevik $hotelRoomOfferBronevik;

    private HotelBronevik $hotelBronevik;

    public function __construct()
    {
        $this->bronevik = new Bronevik();
        $this->hotelRoomBronevi = new HotelRoomBronevik();
        $this->hotelBronevik = new HotelBronevik();
        $this->hotelRoomOfferBronevik = new HotelRoomOfferBronevik();
    }

    /**
     * @param $sectionId - ид раздела в каталоге битрикса
     * @param $externalId - внешний ИД отеля в бронивике
     * @param $guests - кол-во гостей
     * @param $arChildrenAge - массив. [8,4] - возраст детей
     * @param $dateFrom - Дата от
     * @param $dateTo - Дата до
     * @param $minChildAge - null - минимальный возраст ребенка? Идет из параметра отеля судя по всему UF_MIN_CHIELD_AGE
     * @return array
     * @throws \SoapFault
     */
    public function __invoke($sectionId, $externalId, $guests, $arChildrenAge, $dateFrom, $dateTo, $minChildAge): array
    {
        $searchCriteria = [];
        $criterionPaymentRecipient = new SearchOfferCriterionPaymentRecipient;
        $criterionPaymentRecipient->addPaymentRecipient('agency');
        $searchCriteria[] = $criterionPaymentRecipient;

        if (count($arChildrenAge) || $guests > 0) {
            $criterionNumberOfGuests = new SearchOfferCriterionNumberOfGuests();
            $criterionNumberOfGuests->setAdults($guests);

            if (count($arChildrenAge)) {
                foreach ($arChildrenAge as $childAge) {
                    $child = new Child();
                    $child->setAge($childAge);
                    $child->setCount(1);
                    $criterionNumberOfGuests->addChild($child);
                }
            }

            $searchCriteria[] = $criterionNumberOfGuests;
        }


        $onlyOnline = new SearchOfferCriterionOnlyOnline();
        $searchCriteria[] = $onlyOnline;

        $skipElements = new SkipElements();
        $skipElements->addElement('dailyPrices');

        $result = $this->bronevik->searchHotelOffersResponse(
            date('Y-m-d', strtotime($dateFrom)),
            date('Y-m-d', strtotime($dateTo)),
            CurrencyCodes::RUB,
            null,
            $searchCriteria,
            [$externalId],
            $skipElements->getElement(),
        );

        $this->updateHotels($result);

        $offers = $this->saveOffers($result);

        $hotelSectionService = $this->hotelBronevik->showByExternalId($externalId);

        $rooms = $this->hotelRoomBronevi->list(['SECTION_ID' => $hotelSectionService['ID']]);

        $rooms = $this->appendOfferInRooms($rooms, $offers);

        return [
            'arItems' => $rooms,
            'error' => ! count($offers) ? 'Не найдено номеров на выбранные даты' : '',
        ];
    }

    private function appendOfferInRooms(array $rooms, array $offers): array
    {
        $result = [];
        $offersGroupBy = $this->offersGroupByRoomId($offers);
        foreach ($rooms as $room) {
            $room['OFFERS'] = $offersGroupBy[$room['ID']];
            $result[$room['ID']][] = $room;
        }

        return $result;
    }

    private function offersGroupByRoomId($offers): array
    {
        $result = [];

        foreach ($offers as $offer) {
            $result[$offer['PROPERTIES']['ROOM_ID']['VALUE']][] = $offer;
        }

        return $result;
    }

    private function updateHotels(Hotels $hotels): void
    {
        /** @var HotelWithOffers $hotel */
        foreach ($hotels->hotel as $hotel) {
            $data = [
                'UF_INFORMATIONS' => json_encode($hotel?->informationForGuest?->notification),
                'UF_TAXES' => $hotel->hasTaxes ? json_encode($hotel?->taxes?->tax) : '',
                'UF_ADDITIONAL_INFO' => json_encode($hotel->additionalInfo),
                'UF_TIME_FROM' => json_encode($hotel->additionalInfo),
                'UF_TIME_TO' => json_encode($hotel->additionalInfo),
                'UF_ALLOWABLE_TIME' => json_encode(['allowableCheckinTime' => $hotel?->allowableCheckinTime, 'allowableCheckoutTime' => $hotel?->allowableCheckoutTime]),

            ];
            $this->hotelBronevik->update(null, $hotel->getId(), $data);
        }
    }

    private function saveOffers(Hotels $hotels): array
    {
        $offers = [];
        /** @var HotelWithOffers $hotel */
        foreach ($hotels->hotel as $hotel) {
            /** @var HotelOffer $offer */
            foreach ($hotel->offers->offer as $offer) {
                $data = $this->getDataByOffer($offer);
                $offers[] = $this->upsertOffer($data);
            }
        }

        return $offers;
    }

    private function getDataByOffer(HotelOffer $offer)
    {
        $cancellationPolicies = [];
        /** @var HotelOfferCancellationPolicy $policy */
        foreach ($offer->cancellationPolicies as $policy) {
            $cancellationPolicies[] = 'После ' . date('d.m.Y H:i T', strtotime($policy->getPenaltyDateTime())) . ' штраф ' . $policy->penaltyPriceDetails->clientCurrency->gross->getPrice() . ' ' . $policy->penaltyPriceDetails->clientCurrency->gross->getCurrency();
        }

        $meals = [];
        /** @var AvailableMeal $meal */
        foreach ($offer->meals->meal as $meal) {
            $id = $meal->getId();
            $mealName = $this->bronevik->getMeal($id);
            $meals[] = $mealName . ' ' . ($meal->getIncluded() ? 'включен' : 'не включен') . ' - ' . $meal->priceDetails->clientCurrency->gross->getPrice() . ' ' . $meal->priceDetails->clientCurrency->gross->getCurrency();
        }

        $offerPolicies = [];
        /** @var OfferPolicy $policy */
        foreach ($offer->offerPolicies->policy as $policy) {
            $offerPolicies[] = $policy->value;
        }

        return [
            'NAME' => $offer->name,
            'IBLOCK_ID' => CATALOG_BRONEVIK_OFFERS_IBLOCK_ID,
            'PROPERTY_VALUES' => [
                'CODE' => $offer->code,
                'NAME_CATEGORY' => $offer->name,
                'RATE_TYPE' =>  $this->getRateType($offer->rateType),
                'NON_REFUNDABLE' => $this->getPropertyListId(CATALOG_BRONEVIK_OFFERS_IBLOCK_ID, 'NON_REFUNDABLE',$offer->nonRefundable ? 'Y' : 'N'),
                'PRICE' => $offer->priceDetails->client->clientCurrency->gross->getPrice(),// . ' ' . $offer->priceDetails->client->clientCurrency->gross->getCurrency(),
                'IMMEDIATE_CONFIRMATION' => $this->getPropertyListId(CATALOG_BRONEVIK_OFFERS_IBLOCK_ID, 'IMMEDIATE_CONFIRMATION',$offer->immediateConfirmation ? 'Y' : 'N'),
                'FREE_ROOMS' => $offer->freeRooms,
                'CANCELLATION_POLICIES' => implode('<br />', $cancellationPolicies),
                'CANCELLATION_POLICIES_JSON' => json_encode($offer->cancellationPolicies),
                'EXTERNAL_ROOM_ID' => $offer->roomId,
                'ROOM_ID' => $this->getElementRoomId($offer->roomId),
                'ROOM_TYPE' => $offer->roomType,
                'IS_SHARED_ROOM' => $this->getPropertyListId(CATALOG_BRONEVIK_OFFERS_IBLOCK_ID, 'IS_SHARED_ROOM',$offer->isSharedRoom ? 'Y' : 'N'),
                'IS_BLOCK_ROOM' => $this->getPropertyListId(CATALOG_BRONEVIK_OFFERS_IBLOCK_ID, 'IS_BLOCK_ROOM',$offer->isBlockRoom ? 'Y' : 'N'),
                'PAYMENT_RECIPIENT' => $offer->paymentRecipient,
                'GUARANTEE_TYPE' => $offer->guaranteeType,
                'MEALS' => implode('<br />', $meals),
                'MEALS_JSON' => json_encode($offer->meals->meal),
                'OFFER_POLICIES' => implode('<br />', $offerPolicies),
                'DEEP_LINK' => $offer->deepLink,
                'ROOM_WITH_WINDOW' => $offer->roomWithWindow,
            ],
        ];
    }

    private function getElementRoomId(int $id): ?int
    {
        if ($result = $this->hotelRoomBronevi->showByExternalId($id)) {
            return $result['ID'];
        }

        return null;
    }

    private function upsertOffer(array $offer)
    {
        $code = $offer['PROPERTY_VALUES']['CODE'];
        $arExistElement = $this->hotelRoomOfferBronevik->list(["PROPERTY_CODE" => $code], false);

        $iE = new CIBlockElement;
        if (count($arExistElement)) {
            $itemId = $arExistElement[0]['ID'];
            $iE->Update($itemId, $offer);
        } else {
            $itemId = $iE->Add($offer);
        }

        return current($this->hotelRoomOfferBronevik->list(["ID" => $itemId], false));
    }

    private function getRateType(RateType $rateType)
    {
        $entity = HighloadBlockTable::compileEntity(BRONEVIK_RATE_TYPE_HL_ENTITY);
        $entityDataClass = $entity->getDataClass();

        $existingValue = $entityDataClass::getList([
            'select' => ['ID'],
            'filter' => ['=UF_NAME' => $rateType->rateName], // Замените UF_NAME на ваше поле
            'limit' => 1
        ])->fetch();

        if (! $existingValue) {
            $data = [
                'UF_NAME' => $rateType->rateName,
                'UF_DESCRIPTION' => $rateType->rateDescription,
                'UF_XML_ID' => $rateType->rateName,
            ];

            $entityDataClass::add($data);
        }

        return $rateType->rateName;
    }

    private function getPropertyListId(int $iblockId, string $code, string $xmlId)
    {
        $propertyEnums = \CIBlockPropertyEnum::GetList(
            [],
            ["IBLOCK_ID" => $iblockId, "CODE" => $code, "XML_ID" => $xmlId]
        );

        if ($enumFields = $propertyEnums->Fetch())
            return $enumFields['ID'];

        return false;
    }
}