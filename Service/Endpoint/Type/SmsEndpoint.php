<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint\Type;

use EMS\FormBundle\Service\Endpoint\EndpointInterface;

final class SmsEndpoint implements EndpointTypeInterface
{
    public function canExecute(EndpointInterface $endpoint): bool
    {
        return 'sms' === $endpoint->getServiceType();
    }

    public function execute(EndpointInterface $endpoint): int
    {
        return 0;
    }
}