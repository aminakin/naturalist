<?php

namespace Logger\Loggers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Logger;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Naturalist\Telegram\DebugBot;

class TelegramLogger extends Logger
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    protected function logMessage(string $level, string $message): void
    {
        $telegramBot = DebugBot::bot(DEBUG_TELEGRAM_BOT_TOKEN);
        $telegramBot->sendMessage($message);
    }
}