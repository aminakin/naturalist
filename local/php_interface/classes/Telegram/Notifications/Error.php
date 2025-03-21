<?php
namespace Naturalist\Telegram\Notifications;
use Naturalist\Telegram\DebugBot;
use Naturalist\Telegram\TelegramBot;

class Error {
    private static TelegramBot $bot;

    private function setBot():void{
        self::$bot = DebugBot::bot(DEBUG_TELEGRAM_BOT_TOKEN);
    }

    private function getBot():TelegramBot{
        self::setBot();
        return self::$bot;
    }
    private static function sectionByHotelId($hotelId): array {
        $cacheKey = 'hotel_sections_' . $hotelId;
        $cacheDuration = 7200;
        $cachedData = apcu_fetch($cacheKey);

        if (!$cachedData) {
            return $cachedData;
        }

        $items = [];

        $data = CIBlockSection::GetList(
            false,
            array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "UF_EXTERNAL_ID" => $hotelId),
            false,
            array("ID", "NAME"),
            false
        );

        if ($section = $data->Fetch()) {
            $items[] = [
                'ID' => $section['ID'],
                'NAME' => $section['NAME'],
            ];

            apcu_store($cacheKey, $items, $cacheDuration);
        } else {
            throw new \Exception("ошибка при выборке раздела отелей ");
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