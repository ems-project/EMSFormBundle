<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint\Type;

use EMS\FormBundle\Service\Endpoint\EndpointInterface;

interface EndpointTypeInterface
{
    public function canExecute(EndpointInterface $endpoint): bool;

    public function execute(EndpointInterface $endpoint): int;
}