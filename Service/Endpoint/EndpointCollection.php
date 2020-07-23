<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

use Psr\Log\LoggerInterface;

final class EndpointCollection
{
    /** @var array<mixed> */
    private $config;
    /** @var LoggerInterface */
    private $logger;
    /** @var Endpoint[] */
    private $endpoints;

    /**
     * @param array<mixed> $config
     */
    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->endpoints = [];
    }

    public function getByFieldName(string $fieldName): ?Endpoint
    {
        foreach ($this->getEndpoints() as $endpoint) {
            if ($fieldName === $endpoint->getFieldname()) {
                return $endpoint;
            }
        }

        return null;
    }

    /**
     * @return Endpoint[]
     */
    private function getEndpoints(): array
    {
        if (count($this->endpoints) > 0) {
            return $this->endpoints;
        }

        foreach ($this->config as $config) {
            try {
                $this->endpoints[] = new Endpoint($config);
            } catch (\Exception $e) {
                $this->logger->error('invalid endpoint configuration', [
                    'config' => $config,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->endpoints;
    }
}
