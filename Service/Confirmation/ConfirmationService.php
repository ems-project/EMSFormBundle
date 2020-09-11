<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Confirmation;

use EMS\FormBundle\FormConfig\ElementInterface;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use EMS\FormBundle\Service\Confirmation\Endpoint\ConfirmationEndpointType;
use EMS\FormBundle\Service\Endpoint\EndpointManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

final class ConfirmationService
{
    /** @var FormConfigFactory */
    private $configFactory;
    /** @var CsrfTokenManager */
    private $csrfTokenManager;
    /** @var LoggerInterface */
    private $logger;
    /** @var EndpointManager */
    private $endpointManager;

    public function __construct(
        FormConfigFactory $configFactory,
        CsrfTokenManager $csrfTokenManager,
        LoggerInterface $logger,
        EndpointManager $endpointManager
    ) {
        $this->configFactory = $configFactory;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->logger = $logger;
        $this->endpointManager = $endpointManager;
    }

    public function validate(string $fieldName, string $confirmValue, string $verificationCode): bool
    {
        try {
            $endpoint = $this->endpointManager->getEndpointByFieldName($fieldName);
            $endpointType = $this->endpointManager->getEndpointType($endpoint);

            if (!$endpointType instanceof ConfirmationEndpointType) {
                $this->logger->error('invalid endpoint type');
                return false;
            }

            return $endpointType->verify($endpoint, $confirmValue, $verificationCode);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function send(ConfirmationRequest $confirmationRequest, string $ouuid)
    {
        try {
            $formConfig = $this->configFactory->create($ouuid, $confirmationRequest->getLocale());
            $codeFieldElement = $this->getConfirmationField($formConfig, $confirmationRequest);

            $endpoint = $this->endpointManager->getEndpointByFieldName($codeFieldElement->getName());
            $endpointType = $this->endpointManager->getEndpointType($endpoint);

            if (!$endpointType instanceof ConfirmationEndpointType) {
                throw new \Exception('invalid endpoint type');
            }

            return $endpointType->confirm($endpoint, $formConfig, $confirmationRequest->getValue());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    private function getConfirmationField(FormConfig $formConfig, ConfirmationRequest $confirmationRequest): ElementInterface
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
}
