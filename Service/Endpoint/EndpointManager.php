<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

use EMS\FormBundle\Service\Endpoint\Type\EndpointTypeInterface;
use Psr\Log\LoggerInterface;

final class EndpointManager
{
    /** @var array<mixed> */
    private $config;
    /** @var LoggerInterface */
    private $logger;
    /** @var EndpointInterface[] */
    private $endpoints = [];
    /** @var EndpointTypeInterface[] */
    private $endpointTypes = [];

    /**
     * @param array<mixed> $envConfig
     */
    public function __construct(
        array $envConfig,
        \Traversable $endpointTypes,
        LoggerInterface $logger
    ) {
        $this->config = $envConfig;
        $this->endpointTypes = $endpointTypes;
        $this->logger = $logger;
    }

    public function getByFieldName(string $fieldName): ?EndpointInterface
    {
        foreach ($this->getEndpoints() as $endpoint) {
            if ($fieldName === $endpoint->getFieldname()) {
                return $endpoint;
            }
        }

        return null;
    }

    /**
     * @return EndpointInterface[]
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
