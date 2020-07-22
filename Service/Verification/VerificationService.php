<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Verification;

use EMS\ClientHelperBundle\Helper\Api\ApiService;
use EMS\ClientHelperBundle\Helper\Api\Client;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

final class VerificationService
{
    /** @var ApiService */
    private $apiService;
    /** @var ClientRequestManager */
    private $clientRequestManager;
    /** @var CsrfTokenManager */
    private $csrfTokenManager;
    /** @var FormConfigFactory */
    private $configFactory;

    public function __construct(
        ApiService $apiService,
        ClientRequestManager $clientRequestManager,
        CsrfTokenManager $csrfTokenManager,
        FormConfigFactory $configFactory
    ) {
        $this->apiService = $apiService;
        $this->clientRequestManager = $clientRequestManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->configFactory = $configFactory;
    }

    public function create(CreateRequest $request, string $ouuid): string
    {
        $config = $this->configFactory->create($ouuid, $request->getLocale());
        $codeField = $config->getElementByName($request->getCodeField());

        if (null === $codeField) {
            throw new \Exception('code field not found!');
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($codeField->getId(), $request->getToken()))) {
            throw new \Exception('invalid csrf token');
        }

        $verificationCode = $this->getApiClient()->createFormVerification($request->getValue());

        if (null === $verificationCode) {
            throw new \Exception('failed creating verification code');
        }

        return $verificationCode;
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
