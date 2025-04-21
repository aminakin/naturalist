<?php


namespace Logger\Formatters;

use Bitrix\Main\Diag\LogFormatter;
use Bitrix\Main\Diag\LogFormatterInterface;
use Naturalist\Markdown;

class MessageLogFormatter extends LogFormatter implements LogFormatterInterface
{

    public function format($message, array $context = []): string
    {
         $message = parent::format($message, $context);

         return Markdown::arrayToMarkdown($message);
    }
}