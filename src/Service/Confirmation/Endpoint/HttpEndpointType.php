<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Confirmation\Endpoint;

use EMS\CommonBundle\Contracts\CoreApi\CoreApiInterface;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\Service\Endpoint\EndpointInterface;
use EMS\FormBundle\Service\Endpoint\EndpointTypeInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HttpEndpointType extends ConfirmationEndpointType implements EndpointTypeInterface
{
    private CoreApiInterface $coreApi;
    private HttpClientInterface $httpClient;
    private SessionInterface $session;
    private TranslatorInterface $translator;

    public const NAME = 'http';

    public function __construct(
        CoreApiInterface $coreApi,
        HttpClientInterface $httpClient,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->coreApi = $coreApi;
        $this->httpClient = $httpClient;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function canExecute(EndpointInterface $endpoint): bool
    {
        return self::NAME === $endpoint->getType();
    }

    public function getVerificationCode(EndpointInterface $endpoint, string $confirmValue): ?string
    {
        if ($endpoint->saveInSession()) {
            $verificationCode = $this->session->get($this->getSessionKey($confirmValue), false);
        } else {
            $verificationCode = $this->coreApi->form()->getVerification($confirmValue);
        }

        return \is_string($verificationCode) ? $verificationCode : null;
    }

    public function confirm(EndpointInterface $endpoint, FormConfig $formConfig, string $confirmValue): bool
    {
        $verificationCode = $this->createVerificationCode($endpoint, $confirmValue);
        $replaceBody = ['%value%' => $confirmValue, '%verification_code%' => $verificationCode];

        if (null !== $messageTranslationKey = $endpoint->getMessageTranslationKey()) {
            $messageTranslation = $this->translator->trans(
                $messageTranslationKey,
                $replaceBody,
                $formConfig->getTranslationDomain()
            );

            $replaceBody = \array_merge(['%message_translation%' => $messageTranslation], $replaceBody);
        }

        $response = $this->request($endpoint, $replaceBody);

        $result = \json_decode($response->getContent(), true);

        if (!\is_array($result) || !isset($result['ResultCode']) || 0 !== $result['ResultCode']) {
            throw new \Exception(\sprintf('Invalid endpoint response %s', $response->getContent()));
        }

        return true;
    }

    /**
     * @param array<string, string> $replaceBody
     */
    public function request(EndpointInterface $endpoint, array $replaceBody, int $timeout = 20): ResponseInterface
    {
        $httpRequest = $endpoint->getHttpRequest();

        return $this->httpClient->request($httpRequest->getMethod(), $httpRequest->getUrl(), [
            'headers' => $httpRequest->getHeaders(),
            'body' => $httpRequest->createBody($replaceBody),
            'max_duration' => $timeout,
        ]);
    }

    private function createVerificationCode(EndpointInterface $endpoint, string $confirmValue): string
    {
        if (!$endpoint->saveInSession()) {
            return $this->coreApi->form()->createVerification($confirmValue);
        }

        $verificationCode = $this->session->get($this->getSessionKey($confirmValue));

        if (null === $verificationCode) {
            $verificationCode = \sprintf('%d%05d', \mt_rand(1, 9), \mt_rand(0, 99999));
            $this->session->set($this->getSessionKey($confirmValue), $verificationCode);
        }

        return $verificationCode;
    }

    private function getSessionKey(string $value): string
    {
        return \sprintf('EMS_CC_[%s]', $value);
    }
}
