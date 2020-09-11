<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Confirmation\Endpoint;

use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\Service\Endpoint\EndpointInterface;
use EMS\FormBundle\Service\Endpoint\EndpointTypeInterface;

abstract class ConfirmationEndpointType implements EndpointTypeInterface
{
    /**
     * Called for sending a confirmation code
     */
    abstract public function confirm(EndpointInterface $endpoint, FormConfig $formConfig, string $confirmValue): bool;

    /**
     * Called on submit for validating the given verification code
     */
    abstract public function verify(EndpointInterface $endpoint, string $confirmValue, string $verificationCode): bool;
}
