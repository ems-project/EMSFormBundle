<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

use EMS\FormBundle\Service\Confirmation\Endpoint\HttpEndpointType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Endpoint implements EndpointInterface
{
    /** @var string */
    private $fieldName;
    /** @var string|null */
    private $messageTranslationKey;
    /** @var HttpRequest */
    private $httpRequest;
    /** @var bool */
    private $saveSession;
    /** @var string */
    private $type;

    /** @param array<string, mixed> $config */
    public function __construct(array $config)
    {
        $config = $this->getOptionsResolver()->resolve($config);

        $this->fieldName = $config['field_name'];
        $this->httpRequest = new HttpRequest($config['http_request']);
        $this->messageTranslationKey = $config['message_translation_key'] ?? null;
        $this->saveSession = $config['save_session'] ?? true;
        $this->type = $config['type'];
    }

    public function getType(): string
    {
        return $this->type;
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

    public function saveInSession(): bool
    {
        return $this->saveSession;
    }

    private function getOptionsResolver(): OptionsResolver
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setRequired(['field_name'])
            ->setDefaults([
                'message_translation_key' => null,
                'http_request' => [],
                'type' => HttpEndpointType::NAME,
                'save_session' => true,
            ]);

        return $optionsResolver;
    }
}
