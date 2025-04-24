<?php
use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(null, [
    '\Logger\Formatters\JsonLogFormatter' => '/local/php_interface/lib/logger/classes/formatters/jsonlogformatter.php',
    '\Logger\Formatters\MessageLogFormatter' => '/local/php_interface/lib/logger/classes/formatters/messagelogformatter.php',
    '\Logger\Channels\LoggerChannel' => '/local/php_interface/lib/logger/classes/channels/loggerchannel.php',
    '\Logger\Loggers\TelegramLogger' => '/local/php_interface/lib/logger/loggers/telegramlogger.php',
    '\Logger\Interfaces\LoggerChannelInterface' => '/local/php_interface/lib/logger/interfaces/loggerchannelinterface.php',
]);