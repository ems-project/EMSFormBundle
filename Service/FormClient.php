<?php

namespace EMS\FormBundle\Service;


use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\FormBundle\Components\FormConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

//TODO nice to have: generator bundle for backend form config (structure, validations, fields, domains)
class FormClient
{
    /** @var ClientRequest */
    private $client;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ClientRequest $client, FormFactoryInterface $formFactory, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->formFactory = $formFactory;
        $this->logger = $logger;
    }

    public function getAllowedDomains(string $id): array
    {
        return array_map(
            function ($domain){
                return $domain['domain'];
            },
            //TODO fetch type from config
            ($this->client->get('form_domain', $id))['_source']['allowed_domains']
        );
    }

    public function getForm(string $id, string $locale): FormInterface
    {
        //TODO: fetch type from config
        $result = ($this->client->get('form_structure', $id))['_source'];
        $configuration = new FormConfiguration($this->loadReferencedFieldsAndValidations($result), $id, $locale);

        foreach ($configuration->getFailures() as $failure) {
            $this->logger->error($failure);
        }

        return $configuration->getForm($this->formFactory);
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
            function ($validationDefinition){
                $validationDefinition['validation'] = $this->client->getByEmsKey($validationDefinition['validation']);
                return $validationDefinition;
            },
            $validations
        );
    }
}