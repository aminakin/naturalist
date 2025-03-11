<?php

namespace Naturalist\Telegram;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Naturalist\Http\CurlHttpFetch;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Highloadblock\HighloadBlock;
use Bitrix\Main\Loader;

class TelegramBot
{
    protected string $telegramApi;

    protected CurlHttpFetch $http;
    protected static ?self $instance;

    protected function __construct(
        protected readonly string $telegramToken
    ) {
        $this->telegramApi = 'https://api.telegram.org/bot' . $this->telegramToken;
        $this->http = new CurlHttpFetch();

    }

    public static function bot(string $token):self{
        if(!isset(self::$instance) || self::$instance === null){
            self::$instance = new self($token);
        }
        return self::$instance;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    protected function getUsers(): array {
        $hiLoadId = 34;

        if (!Loader::includeModule("highloadblock")) {
            throw new \Exception("Не удалось подключить модуль highloadblock.");
        }
        $hiLoad = HighloadBlockTable::getById($hiLoadId)->fetch();
        if ($hiLoad) {
            $dataClass = HighloadBlockTable::compileEntity($hiLoad)->getDataClass();

            $result = $dataClass::getList([
                'select' => ['UF_CHAT_ID'],
                'order' => ['UF_CHAT_ID' => 'ASC']
            ]);

            $response = [];

            while ($item = $result->fetch()) {
                $response[] = $item['UF_CHAT_ID'];
            }
            return $response;
        }

        return [];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function sendMessage(string $text): void
    {
        $url = $this->telegramApi . "/sendMessage";

        foreach ($this->getUsers() as $chatId){
            $this->http->post($url,[
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'MarkdownV2'
            ]);
        }

    }
}