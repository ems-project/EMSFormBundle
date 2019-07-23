<?php

namespace EMS\FormBundle\Service;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\CommonBundle\Common\EMSLink;
use EMS\FormBundle\Components\FormConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

//TODO nice to have: generator bundle for backend form config (instance, structure, validations, fields, domains)
class FormClient
{
    /** @var ClientRequest */
    private $client;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $domainType;

    /** @var string */
    private $instanceType;

    /** @var string */
    private $formField;

    /** @var string */
    private $themeField;

    public function __construct(
        ClientRequest $client,
        FormFactoryInterface $formFactory,
        LoggerInterface $logger,
        string $domainType,
        string $instanceType,
        string $formField,
        string $themeField
    ) {
        $this->client = $client;
        $this->formFactory = $formFactory;
        $this->logger = $logger;
        $this->domainType = $domainType; //@todo remove deprecated, we have an emsLink
        $this->instanceType = $instanceType;
        $this->formField = $formField;
        $this->themeField = $themeField;
    }

    public function getFormInstance(FormConfiguration $configuration): FormInterface
    {
        return $configuration->getForm($this->formFactory);
    }

    public function getFormConfiguration(string $ouuid, string $locale)
    {
        $result = ($this->client->get($this->instanceType, $ouuid))['_source'];

        $domains = $this->getAllowedDomains($result['domain']);
        $configuration = new FormConfiguration($this->loadFormStructure($result), $this->themeField, $ouuid, $locale, $domains);

        foreach ($configuration->getFailures() as $failure) {
            $this->logger->error($failure);
        }

        return $configuration;
    }

    public function getCacheKey(): string
    {
        return $this->client->getCacheKey();
    }

    private function getAllowedDomains(string $emsId): array
    {
        $emsLink = EMSLink::fromText($emsId);

        return array_values(array_map(
            function ($domain) {
                return $domain['domain'];
            },
            ($this->client->get($emsLink->getContentType(), $emsLink->getOuuid()))['_source']['allowed_domains']
        ));
    }

    private function loadFormStructure(array $formDefinition): array
    {
        if (!array_key_exists($this->formField, $formDefinition)) {
            return [];
        }

        $formStructure = ($this->client->getByEmsKey($formDefinition[$this->formField]))['_source'];
        $formDefinition[$this->formField] = $this->loadReferencedFieldsAndValidations($formStructure);
        return $formDefinition;
    }

    private function loadReferencedFieldsAndValidations(array $formDefinition): array
    {
        if (!array_key_exists('fields', $formDefinition)) {
            return [];
        }

        $formDefinition['fields'] = array_map(
            function ($field) {
                if (array_key_exists('validations', $field)) {
                    $field['validations'] = $this->replaceValidations($field['validations']);
                }
                return $field;
            },
            $this->replaceFields($formDefinition['fields'])
        );
        return $formDefinition;
    }

    private function replaceFields(array $fields): array
    {
        return array_map(
            function ($fieldDefinition) {
                $fieldDefinition['type'] = $this->client->getByEmsKey($fieldDefinition['type']);
                if (array_key_exists('validations', $fieldDefinition['type']['_source'])) {
                    $fieldDefinition['type']['_source']['validations'] = $this->replaceValidations($fieldDefinition['type']['_source']['validations']);
                }
                return $fieldDefinition;
            },
            $fields
        );
    }

    private function replaceValidations(array $validations): array
    {
        return array_map(
            function ($validationDefinition) {
                $validationDefinition['validation'] = $this->client->getByEmsKey($validationDefinition['validation']);
                return $validationDefinition;
            },
            $validations
        );
    }
}
