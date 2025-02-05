<?php

namespace Naturalist\bronevik\connector;

use Bronevik\HotelsConnector as BronevikHotelsConnector;
use Bronevik\HotelsConnector\Enum\Languages;
use Bronevik\HotelsConnector\Enum\Endpoints ;
use Exception;
use COption;

class HotelsConnector extends BronevikHotelsConnector
{
    private static string $login = 'naturalist_test';

    private static string $password = 'v6WA7ZF';

    private static string $clientKey = 'zdcobyklj280bkj37sz3u5p29p59';

    private static self $instance;

    /**
     * @throws \Exception
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$login = COption::GetOptionString( 'add_object_bronevik', 'login', 'naturalist_test');
            self::$password = COption::GetOptionString( 'add_object_bronevik', 'password', 'naturalist_test');
            self::$clientKey = COption::GetOptionString( 'add_object_bronevik', 'key', 'naturalist_test');
            $stand = COption::GetOptionString( 'add_object_bronevik', 'stand', Endpoints::SECURE_DEVELOPMENT);

            $secureEndpoint = Endpoints::SECURE_DEVELOPMENT;
            $debugMode = true;
            if ($stand == Endpoints::PRODUCTION) {
                $secureEndpoint = Endpoints::SECURE_PRODUCTION;
                $debugMode = false;
            }

            self::$instance = new self($stand, $secureEndpoint,$debugMode);
            self::$instance->setCredentials(self::$login, self::$password, self::$clientKey);
            self::$instance->setLanguage(Languages::RUSSIAN);
        }

        if (!(self::$instance instanceof BronevikHotelsConnector) || self::$instance === null) {
            throw new Exception('Ошибка инициализации BronevikHotelsConnector');
        }

        return self::$instance;
    }
}