<?php

namespace Naturalist\Telegram;

use Natural\Http\CurlHttpFetch;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Highloadblock\HighloadBlock;

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

    private function getUsers():array{
        $hiLoadId = 34;
        $hiLoad = HighloadBlockTable::getById($hiLoadId)->fetch();
        if($hiLoad){
            $entity = HighloadBlock::compileEntity($hiLoad);
            $dataClass = $entity->getDataClass();

            $result = $dataClass::getList([
                'select' => ['UF_CHAT_ID'],
                'order' => ['UF_CHAT_ID' => 'ASC']
            ]);

            $response = [];

            while($item = $result->fetch()){
                $response[] = $item['UF_CHAT_ID'];
            }
            return $response;
        }
        return [];
    }

    public function sendMessage(string $text): void
    {
        $url = $this->telegramApi . "/sendMessage";

        foreach ($this->getUsers() as $user){
            $this->http->post($url,$user);
        }

    }
}