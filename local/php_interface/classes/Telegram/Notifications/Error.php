<?php
namespace Naturalist\Telegram\Notifications;
use CIBlockSection;
use Naturalist\Telegram\DebugBot;
use Naturalist\Telegram\TelegramBot;
use Bitrix\Main\Data\Cache;
class Error {
    private static TelegramBot $bot;

    private static  function setBot():void{
        self::$bot = DebugBot::bot(DEBUG_TELEGRAM_BOT_TOKEN);
    }

    private static function getBot():TelegramBot{
        self::setBot();
        return self::$bot;
    }

    private static function sectionByHotelId($hotelId): array {
        $cache = Cache::createInstance();
        $cacheKey = 'hotel_sections_' . $hotelId;
        $cacheDuration = 7200;

        if ($cache->initCache($cacheDuration, $cacheKey)) {
            $items = $cache->getVars();
        } else {
            $items = [];

            $data = CIBlockSection::GetList(
                false,
                array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "UF_EXTERNAL_ID" => $hotelId),
                false,
                array("ID", "NAME"),
                false
            );

            if($section = $data->Fetch()) {
                $items[] = [
                    'ID' => $section['ID'],
                    'NAME' => $section['NAME'],
                ];
            }

            if (!empty($items)) {
                $cache->startDataCache();
                $cache->endDataCache($items);
            } else {
                throw new \Exception("Ошибка при выборке раздела отелей / ошибка кеширования");
            }
        }
        return $items;
    }

    public static function internal(int|string $hotelId, string $service, string $message):void{
        try{
            self::getBot()->sendMessage("
            Нет ответа от ".$service.", при запросе данных
            Объект: ".self::sectionByHotelId($hotelId)['NAME']."
            ID: ".self::sectionByHotelId($hotelId)['ID']."
            link:
            Тип запроса:
            Сообщение от ".$service.": ".$message."
            ");
        }catch(\Exception $ex){
            throw new \Exception("Ошибка отправвки уведомления: ". $ex->getMessage());
        }
    }

    public static function objectDisabled(int|string $hotelId, string $service):void{
        try{
            self::getBot()->sendMessage("
            Объект отключен от вашего канала продаж:
            Объект: ".self::sectionByHotelId($hotelId)['NAME']."
            ID: ".self::sectionByHotelId($hotelId)['ID']."
            link:
            Сервис: ".$service."
            ");
        }catch(\Exception $ex){
            throw new \Exception("Ошибка отправвки уведомления: ". $ex->getMessage());
        }
    }

    public static function notTariffs(int|string $hotelId, string $service):void{
        try{
            self::getBot()->sendMessage("
            По объекту нет доступных тарифов:
            Объект: ".self::sectionByHotelId($hotelId)['NAME']."
            ID: ".self::sectionByHotelId($hotelId)['ID']."
            link:
            Сервис: ".$service."
            ");
        }catch(\Exception $ex){
            throw new \Exception("Ошибка отправвки уведомления: ". $ex->getMessage());
        }
    }

    public static function notFreeNums(int|string $hotelId, string $service):void{
        try{
            self::getBot()->sendMessage("
            Ошибка при проверке данных по объекту о доступности, отсутвуют номера на сайте:
            Объект: ".self::sectionByHotelId($hotelId)['NAME']."
            ID: ".self::sectionByHotelId($hotelId)['ID']."
            Тип запроса: availability
            link:
            Сервис: ".$service."
            ");
        }catch(\Exception $ex){
            throw new \Exception("Ошибка отправвки уведомления: ". $ex->getMessage());
        }
    }
}