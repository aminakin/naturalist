<?php

namespace Naturalist;

use Exception;
use Naturalist\bronevik\BronevikSearchService;
use Object\Uhotels\Data\Search;

class SearchServiceFactory
{
    private array $services = [];

    private const SERVICE_MAP = [
        'traveline' => Traveline::class,
        'bnovo' => Bnovo::class,
        'bronevik' => BronevikSearchService::class,
        'uhotels' => Search::class,
    ];

    public function create(string $type): SearchServiceInterface
    {
        $this->validateServiceType($type);

        if (!isset($this->services[$type])) {
            $this->initializeService($type);
        }

        return $this->services[$type];
    }

    private function validateServiceType(string $type): void
    {
        if (!array_key_exists($type, self::SERVICE_MAP)) {
            throw new Exception(
                "Unknown search service type: {$type}. " .
                "Allowed types: " . implode(', ', array_keys(self::SERVICE_MAP))
            );
        }
    }

    private function initializeService(string $type): void
    {
        $className = self::SERVICE_MAP[$type];

        if (!is_subclass_of($className, SearchServiceInterface::class)) {
            throw new \RuntimeException(
                "Service {$className} must implement SearchServiceInterface"
            );
        }

        $this->services[$type] = new $className();

        if (method_exists($this->services[$type], 'initialize')) {
            $this->services[$type]->initialize();
        }
    }
}