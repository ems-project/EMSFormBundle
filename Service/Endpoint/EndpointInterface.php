<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

interface EndpointInterface
{
    public function getServiceType(): string;
}