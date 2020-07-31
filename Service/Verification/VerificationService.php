<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Verification;

use EMS\ClientHelperBundle\Helper\Api\ApiService;
use EMS\ClientHelperBundle\Helper\Api\Client;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class VerificationService
{
    /** @var ApiService */
    private $apiService;
    /** @var ClientRequestManager */
    private $clientRequestManager;
    /** @var SessionInterface */
    private $session;
    /** @var bool */
    private $savedInSession;

    public function __construct(ApiService $apiService, ClientRequestManager $clientRequestManager, SessionInterface $session, bool $savedInSession = true)
    {
        $this->apiService = $apiService;
        $this->clientRequestManager = $clientRequestManager;
        $this->session = $session;
        $this->savedInSession = $savedInSession;
    }

    public function create(string $value): ?string
    {
        if (!$this->savedInSession) {
            return $this->getApiClient()->createFormVerification($value);
        }

        $verificationCode =  $this->session->get($this->getSessionKey($value), null);

        if ($verificationCode === null) {
            $verificationCode = \sprintf("%06d", \mt_rand(1, 999999));
            $this->session->set($this->getSessionKey($value), $verificationCode);
        }

        return $verificationCode;
    }

    public function verify(string $value, string $expectedCode): bool
    {
        if ($this->savedInSession) {
            $verificationCode =  $this->session->get($this->getSessionKey($value), false);
        } else {
            $verificationCode = $this->getApiClient()->getFormVerification($value);
        }

        return $verificationCode === $expectedCode;
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
