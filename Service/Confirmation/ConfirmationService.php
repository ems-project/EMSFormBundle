<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Confirmation;

use EMS\FormBundle\FormConfig\ElementInterface;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use EMS\FormBundle\Service\Endpoint\Endpoint;
use EMS\FormBundle\Service\Endpoint\EndpointCollection;
use EMS\FormBundle\Service\Verification\VerificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ConfirmationService
{
    /** @var FormConfigFactory */
    private $configFactory;
    /** @var CsrfTokenManager */
    private $csrfTokenManager;
    /** @var HttpClientInterface */
    private $httpClient;
    /** @var LoggerInterface */
    private $logger;
    /** @var VerificationService */
    private $verificationService;
    /** @var EndpointCollection */
    private $endpoints;

    public function __construct(
        FormConfigFactory $configFactory,
        CsrfTokenManager $csrfTokenManager,
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        VerificationService $verificationService,
        EndpointCollection $endpoints
    ) {
        $this->configFactory = $configFactory;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->verificationService = $verificationService;
        $this->endpoints = $endpoints;
    }

    public function send(ConfirmationRequest $confirmationRequest, string $ouuid): bool
    {
        try {
            $codeField = $this->getCodeField($confirmationRequest, $ouuid);
            $request = $this->getEndPoint($codeField->getName())->getHttpRequest();

            $verificationCode = $this->verificationService->create($confirmationRequest->getValue());

            if (null === $verificationCode) {
                throw new \Exception('Failed getting verification code');
            }

            $body = $request->createBody($confirmationRequest->getValue(), $verificationCode);

            $response = $this->httpClient->request($request->getMethod(), $request->getUrl(), [
                'headers' => $request->getHeaders(),
                'body' => $body
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * @return array{message: string, to: string}
     */
    public function getMessage(ConfirmationRequest $confirmationRequest, string $ouuid): ?array
    {
        $codeField = $this->getCodeField($confirmationRequest, $ouuid);
        $endPoint = $this->getEndPoint($codeField->getName());

        return [
            'value' => $confirmationRequest->getValue(),
            'verification_code' => $this->verificationService->create($confirmationRequest->getValue()),
            'field_name' => $endPoint->getFieldName(),
        ];
    }

    private function getEndPoint(string $fieldName): Endpoint
    {
        $endpoint = $this->endpoints->getByFieldName($fieldName);

        if (null === $endpoint) {
            throw new \Exception(sprintf('No valid endpoint found for form field %s', $fieldName));
        }

        return $endpoint;
    }

    private function getCodeField(ConfirmationRequest $confirmationRequest, string $ouuid): ElementInterface
    {
        $config = $this->configFactory->create($ouuid, $confirmationRequest->getLocale());
        $codeField = $config->getElementByName($confirmationRequest->getCodeField());

        if (null === $codeField) {
            throw new \Exception(sprintf('Code field %s not found in form', $confirmationRequest->getCodeField()));
        }

        $this->csrfValidation($codeField, $confirmationRequest);

        return $codeField;
    }

    private function csrfValidation(ElementInterface $codeField, ConfirmationRequest $confirmationRequest): void
    {
        $csrfToken = new CsrfToken($codeField->getId(), $confirmationRequest->getToken());

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new \Exception('invalid csrf token!');
        }
    }
}
