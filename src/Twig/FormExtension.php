<?php

declare(strict_types=1);

namespace EMS\FormBundle\Twig;

use EMS\FormBundle\Service\Endpoint\EndpointManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('emsf_http_call', [EndpointManager::class, 'callHttpEndpoint']),
        ];
    }
}
