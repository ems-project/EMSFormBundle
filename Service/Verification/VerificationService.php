<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Verification;

use EMS\ClientHelperBundle\Helper\Api\ApiService;
use EMS\ClientHelperBundle\Helper\Api\Client;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;

final class VerificationService
{
    /** @var ApiService */
    private $apiService;
    /** @var ClientRequestManager */
    private $clientRequestManager;

    public function __construct(ApiService $apiService, ClientRequestManager $clientRequestManager)
    {
        $this->apiService = $apiService;
        $this->clientRequestManager = $clientRequestManager;
    }

    public function create(string $value): ?string
    {
        return $this->getApiClient()->createFormVerification($value);
    }

    public function verify(string $value, string $expectedCode): bool
    {
        $verificationCode = $this->getApiClient()->getFormVerification($value);

        return $verificationCode === $expectedCode;
    }

    private function getApiClient(): Client
    {
        $apiName = $this->clientRequestManager->getDefault()->getOption('[api][name]');

        return $this->apiService->getApiClient($apiName);
    }
}
