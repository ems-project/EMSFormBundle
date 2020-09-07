<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Confirmation;

use EMS\FormBundle\FormConfig\ElementInterface;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use EMS\FormBundle\Service\Endpoint\Endpoint;
use EMS\FormBundle\Service\Endpoint\EndpointManager;
use EMS\FormBundle\Service\Endpoint\HttpRequest;
use EMS\FormBundle\Service\Verification\VerificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    /** @var EndpointManager */
    private $endpointManager;
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        FormConfigFactory $configFactory,
        CsrfTokenManager $csrfTokenManager,
        HttpClientInterface $httpClient,
        LoggerInterface $logger,
        VerificationService $verificationService,
        EndpointManager $endpointManager,
        TranslatorInterface $translator
    ) {
        $this->configFactory = $configFactory;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->verificationService = $verificationService;
        $this->endpointManager = $endpointManager;
        $this->translator = $translator;
    }

    public function send(ConfirmationRequest $confirmationRequest, string $ouuid): bool
    {
        try {
            $formConfig = $this->getFormConfig($ouuid, $confirmationRequest->getLocale());
            $codeFieldElement = $this->getCodeFieldElement($formConfig, $confirmationRequest);
            $endpoint = $this->getEndPoint($codeFieldElement->getName());

            $verificationCode = $this->verificationService->create($confirmationRequest->getValue());

            if (null === $verificationCode) {
                throw new \Exception('Failed getting verification code');
            }

            $replaceBody = ['%value%' => $confirmationRequest->getValue(), '%verification_code%' => $verificationCode];

            if (null !== $messageTranslationKey = $endpoint->getMessageTranslationKey()) {
                $messageTranslation = $this->translator->trans(
                    $messageTranslationKey,
                    $replaceBody,
                    $formConfig->getTranslationDomain()
                );

                $replaceBody = array_merge(['%message_translation%' => $messageTranslation], $replaceBody);
            }

            return $this->sendSms($endpoint->getHttpRequest(), $replaceBody);
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
        $endPoint = $this->getEndPoint($confirmationRequest->getCodeField());

        return [
            'value' => $confirmationRequest->getValue(),
            'verification_code' => $this->verificationService->create($confirmationRequest->getValue()),
            'field_name' => $endPoint->getFieldName(),
        ];
    }

    private function getEndPoint(string $fieldName): Endpoint
    {
        $endpoint = $this->endpointManager->getByFieldName($fieldName);

        if (null === $endpoint) {
            throw new \Exception(sprintf('No valid endpoint found for form field %s', $fieldName));
        }

        return $endpoint;
    }

    private function getFormConfig(string $ouuid, string $locale): FormConfig
    {
        return $this->configFactory->create($ouuid, $locale);
    }

    private function getCodeFieldElement(FormConfig $formConfig, ConfirmationRequest $confirmationRequest): ElementInterface
    {
        $codeFieldElement = $formConfig->getElementByName($confirmationRequest->getCodeField());

        if (null === $codeFieldElement) {
            throw new \Exception(sprintf('Code field %s not found in form', $codeFieldElement));
        }

        $this->csrfValidation($codeFieldElement, $confirmationRequest->getToken());

        return $codeFieldElement;
    }

    private function csrfValidation(ElementInterface $codeField, string $token): void
    {
        $csrfToken = new CsrfToken($codeField->getId(), $token);

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new \Exception('invalid csrf token!');
        }
    }

    /**
     * @param array<string, string> $replaceBody
     */
    private function sendSms(HttpRequest $httpRequest, array $replaceBody): bool
    {
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
}
