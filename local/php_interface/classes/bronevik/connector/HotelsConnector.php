<?php

namespace Naturalist\bronevik\connector;

use Bronevik\HotelsConnector as BronevikHotelsConnector;
use Bronevik\HotelsConnector\Enum\Languages;
use Bronevik\HotelsConnector\Enum\Endpoints ;


class HotelsConnector extends BronevikHotelsConnector
{
    private static self $instance;

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self(Endpoints::DEVELOPMENT, Endpoints::SECURE_DEVELOPMENT,true);
            self::$instance->setCredentials('naturalist_test', 'v6WA7ZF', 'zdcobyklj280bkj37sz3u5p29p59');
            self::$instance->setLanguage(Languages::RUSSIAN);
        }

        return self::$instance;
    }
}