<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Confirmation\Endpoint;

use EMS\ClientHelperBundle\Helper\Api\ApiService;
use EMS\ClientHelperBundle\Helper\Api\Client;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\Service\Endpoint\EndpointInterface;
use EMS\FormBundle\Service\Endpoint\EndpointTypeInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HttpEndpointType extends ConfirmationEndpointType implements EndpointTypeInterface
{
    /** @var ApiService */
    private $apiService;
    /** @var ClientRequestManager */
    private $clientRequestManager;
    /** @var HttpClientInterface */
    private $httpClient;
    /** @var SessionInterface */
    private $session;
    /** @var TranslatorInterface */
    private $translator;

    public const NAME = 'http';

    public function __construct(
        ApiService $apiService,
        ClientRequestManager $clientRequestManager,
        HttpClientInterface $httpClient,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->apiService = $apiService;
        $this->clientRequestManager = $clientRequestManager;
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
            $verificationCode = $this->getApiClient()->getFormVerification($confirmValue);
        }

        return is_string($verificationCode) ? $verificationCode : null;
    }

    public function confirm(EndpointInterface $endpoint, FormConfig $formConfig, string $confirmValue)
    {
        $verificationCode = $this->createVerificationCode($endpoint, $confirmValue);
        $replaceBody = ['%value%' => $confirmValue, '%verification_code%' => $verificationCode];

        if (null !== $messageTranslationKey = $endpoint->getMessageTranslationKey()) {
            $messageTranslation = $this->translator->trans(
                $messageTranslationKey,
                $replaceBody,
                $formConfig->getTranslationDomain()
            );

            $replaceBody = array_merge(['%message_translation%' => $messageTranslation], $replaceBody);
        }

        $httpRequest = $endpoint->getHttpRequest();

        $response = $this->httpClient->request($httpRequest->getMethod(), $httpRequest->getUrl(), [
            'headers' => $httpRequest->getHeaders(),
            'body' => $httpRequest->createBody($replaceBody),
        ]);

        $result = json_decode($response->getContent(), true);

        if (!is_array($result) || !isset($result['ResultCode']) || 0 !== $result['ResultCode']) {
            throw new \Exception(sprintf('Invalid endpoint response %s', $response->getContent()));
        }

        return true;
    }

    private function createVerificationCode(EndpointInterface $endpoint, string $confirmValue): string
    {
        if (!$endpoint->saveInSession()) {
            $apiVerificationCode = $this->getApiClient()->createFormVerification($confirmValue);

            if (null === $apiVerificationCode) {
                throw new \Exception('unable to generate api client verification code');
            }

            return $apiVerificationCode;
        }

        $verificationCode =  $this->session->get($this->getSessionKey($confirmValue), null);

        if ($verificationCode === null) {
            $verificationCode = \sprintf("%d%05d", \mt_rand(1, 9), \mt_rand(0, 99999));
            $this->session->set($this->getSessionKey($confirmValue), $verificationCode);
        }

        return $verificationCode;
    }

    private function getSessionKey(string $value): string
    {
        return \sprintf('EMS_CC_[%s]', $value);
    }

    private function getApiClient(): Client
    {
        $apiName = $this->clientRequestManager->getDefault()->getOption('[api][name]');

        return $this->apiService->getApiClient($apiName);
    }
}
