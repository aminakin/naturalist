<?php

namespace Naturalist\Telegram;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Naturalist\Http\CurlHttpFetch;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Highloadblock\HighloadBlock;
use Bitrix\Main\Loader;
use CEventLog;

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
    protected function getUsers(): array
    {
        $highloadBlockName = TELEGRAM_USERS_HL_ENTITY;

        if (!Loader::includeModule("highloadblock")) {
            throw new \Exception("Не удалось подключить модуль highloadblock.");
        }

        $hlId = HighloadBlockTable::getList([
            'filter' => ['NAME' => $highloadBlockName],
            'select' => ['ID']
        ])->fetch();

        if ($hlId) {

        $hiLoad = HighloadBlockTable::getById($hlId['ID'])->fetch();
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
    }

        return [];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function sendMessage(string $text, string $type = null): void
    {
        $url = $this->telegramApi . "/sendMessage";

        foreach ($this->getUsers() as $chatId){
            $responce = $this->http->post($url,[
                'chat_id' => $chatId,
                'text' => !is_null($type) ? $type.': '.$text : $text,
                'parse_mode' => 'MarkdownV2'
            ]);

            CEventLog::Add([
                'ITEM_ID' => 'telegramm',
                'DESCRIPTION' => $responce
            ]);
        }

    }
}