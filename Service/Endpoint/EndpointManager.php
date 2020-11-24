<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

use Psr\Log\LoggerInterface;

final class EndpointManager
{
    /** @var array<mixed> */
    private $config;
    /** @var LoggerInterface */
    private $logger;
    /** @var EndpointInterface[] */
    private $endpoints = [];
    /** @var \Traversable|EndpointTypeInterface[] */
    private $endpointTypes;

    /**
     * @param array<mixed>                         $envConfig
     * @param \Traversable|EndpointTypeInterface[] $endpointTypes
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

    public function getEndpointType(EndpointInterface $endpoint): EndpointTypeInterface
    {
        foreach ($this->endpointTypes as $endpointType) {
            if ($endpointType->canExecute($endpoint)) {
                return $endpointType;
            }
        }

        throw new \Exception(\sprintf('Endpoint type "%s" not found!', $endpoint->getType()));
    }

    public function getEndpointByFieldName(string $fieldName): EndpointInterface
    {
        foreach ($this->loadEndpoints() as $endpoint) {
            if ($fieldName === $endpoint->getFieldname()) {
                return $endpoint;
            }
        }

        throw new \Exception(\sprintf('No endpoint found for form field %s', $fieldName));
    }

    /**
     * @return EndpointInterface[]
     */
    private function loadEndpoints(): array
    {
        if (\count($this->endpoints) > 0) {
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
