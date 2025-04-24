<?php

namespace Logger\Channels;

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Diag\Logger;
use Logger\Interfaces\LoggerChannelInterface;
use Psr\Log\LoggerInterface;

class LoggerChannel extends Logger implements LoggerChannelInterface
{
    /** @var array<Logger> $channels */
    private array $channels = [];

    public function addChannel(LoggerInterface $channel): void
    {
        $this->channels[] = $channel;
    }

    protected function logMessage(string $level, string $message): void
    {
        foreach ($this->channels as $channel) {
            $channel->log($level, $this->message, $this->context);
        }
    }

    public static function create(string $id, $params = []): ?Logger
    {
        if ($logger = parent::create($id, $params)) {
            $loggersConfig = Configuration::getValue('loggers');
            if (isset($loggersConfig[$id]))
            {
                $config = $loggersConfig[$id];
                if (isset($config['channels']))
                {
                    foreach ($config['channels'] as $channel) {
                        $channelLogger = Logger::create($channel);
                        $logger->addChannel($channelLogger);
                    }
                }
            }
        }

        return $logger;
    }
}