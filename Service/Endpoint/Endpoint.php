<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class Endpoint implements EndpointInterface
{
    /** @var string */
    private $fieldName;
    /** @var null|string */
    private $messageTranslationKey;
    /** @var HttpRequest */
    private $httpRequest;
    /** @var string */
    private $serviceType;

    public function __construct(array $config)
    {
        $config = $this->getOptionsResolver()->resolve($config);

        $this->fieldName = $config['field_name'];
        $this->httpRequest = new HttpRequest($config['http_request']);
        $this->messageTranslationKey = $config['message_translation_key'] ?? null;
        $this->serviceType = $config['service_type'] ?? 'sms';
    }

    public function getServiceType(): string
    {
        return $this->serviceType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getHttpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    public function getMessageTranslationKey(): ?string
    {
        return $this->messageTranslationKey;
    }

    private function getOptionsResolver(): OptionsResolver
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefaults([
                'message_translation_key' => null,
                'http_request' => [],
                'service_type' => 'sms'
            ])
            ->setRequired(['field_name', 'service_type']);

        return $optionsResolver;
    }
}

