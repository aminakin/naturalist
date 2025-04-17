<?php


namespace Logger\Formatters;

use Bitrix\Main\Diag\LogFormatter;
use Bitrix\Main\Diag\LogFormatterInterface;
use Bitrix\Main\Type\DateTime;

class JsonLogFormatter extends LogFormatter implements LogFormatterInterface
{
    public function format($message, array $context = []): string
    {
        $message = parent::format($message, $context);

        if (!isset($context['delimiter']))
        {
            $context['delimiter'] = static::DELIMITER;
        }

        $message = $this->formatDate(new DateTime()) . ' ' . $message . "\n";

        $data = $this->getContextData($context);

        if (count($data)) {
            $message .= $context['delimiter'] . "\n" . var_export($data, true) . "\n";
        }

        return $message;
    }

    private function getContextData(array $context): array
    {
        $result = $context;
        unset($result['delimiter']);
        unset($result['date']);
        unset($result['exception']);
        unset($result['trace']);
        unset($result['host']);

        return $result;
    }
}