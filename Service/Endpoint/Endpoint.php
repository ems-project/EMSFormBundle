<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

final class Endpoint
{
    /** @var string */
    private $fieldName;
    /** @var null|string */
    private $messageTranslationKey;
    /** @var HttpRequest */
    private $httpRequest;

    public function __construct(array $config)
    {
        $this->fieldName = $config['field_name'];
        $this->httpRequest = new HttpRequest($config['http_request']);
        $this->messageTranslationKey = $config['message_translation_key'] ?? null;
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
}
