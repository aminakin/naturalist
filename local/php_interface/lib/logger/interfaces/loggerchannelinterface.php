<?php

namespace Logger\Interfaces;

use Psr\Log\LoggerInterface;

interface LoggerChannelInterface
{
    public function addChannel(LoggerInterface $channel): void;

//    public static function create(string $id, $params = []);
}