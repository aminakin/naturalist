<?php
namespace Naturalist;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Naturalist\SmartWidgetsController;

class YandexReviews {
    public static function getYandexReviews($sectionIds): array
    {

        Loader::includeModule('highloadblock');
        ini_set('memory_limit', '512M');

        $commonYandexReviewsClass = HighloadBlockTable::compileEntity('YandexReviews')->getDataClass();
        $commonYandexReviews = $commonYandexReviewsClass::query()
            ->addSelect('*')
            ->setOrder(['ID' => 'ASC'])
            ->setFilter(['UF_ID_OBJECT' => $sectionIds])
            ->setCacheTtl(36000000)
            ?->fetchAll();

        $arYandexIDs = array_column($commonYandexReviews, 'UF_ID_YANDEX', 'UF_ID_OBJECT');

        if (is_array($commonYandexReviews) && !empty($commonYandexReviews)) {

            $widgetData = SmartWidgetsController::getWidgetData($arYandexIDs);

            foreach ($commonYandexReviews as &$item) {
                $yandexId = $item['UF_ID_YANDEX'];
                if (isset($widgetData->data->$yandexId)) {
                    $widgetItem = json_decode(json_encode($widgetData->data->$yandexId), true);
                    $item = array_merge($item, $widgetItem);
                }
            }
            unset($item);

            SmartWidgetsController::calculateReviewsSummary($commonYandexReviews);
        }

        return $commonYandexReviews;
    }
}