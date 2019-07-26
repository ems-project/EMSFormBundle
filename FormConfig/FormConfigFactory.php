<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;
use Psr\Log\LoggerInterface;

class FormConfigFactory
{
    /** @var ClientRequest */
    private $client;
    /** @var LoggerInterface */
    private $logger;
    /** @var array */
    private $emsFields;

    public function __construct(ClientRequestManager $manager, LoggerInterface $logger, array $emsFields)
    {
        $this->client = $manager->getDefault();
        $this->logger = $logger;
        $this->emsFields = $emsFields;
    }

    public function create(string $ouuid, string $locale): FormConfig
    {
        $source = $this->client->get($this->emsFields['type'], $ouuid)['_source'];
        $formConfig = new FormConfig($ouuid, $locale, $this->client->getCacheKey());

        if (isset($source[$this->emsFields['theme-field']])) {
            $formConfig->setTheme($source['theme_template']);
        }
        if (isset($source['domain'])) {
            $this->addDomain($formConfig, $source['domain']);
        }

        if (isset($source[$this->emsFields['form-field']])) {
            $this->addForm($formConfig, $source[$this->emsFields['form-field']], $locale);
        }

        return $formConfig;
    }

    private function addDomain(FormConfig $formConfig, string $emsLinkDomain): void
    {
        $domain = $this->getSource($emsLinkDomain, ['allowed_domains']);
        $allowedDomains = $domain['allowed_domains'] ?? [];

        foreach ($allowedDomains as $allowedDomain) {
            $formConfig->addDomain($allowedDomain['domain']);
        }
    }

    private function addField(FormConfig $formConfig, array $source, string $locale): void
    {
        $fieldType = $this->getSource($source['type'], ['class', 'classname', 'validations']);
        $fieldConfig = new FieldConfig($source['technical_name'], $fieldType['id'], $fieldType['classname']);

        $this->addFieldValidations($fieldConfig, $fieldType['validations'] ?? [], $source['validations'] ?? []);

        if (isset($source['choices'])) {
            $this->addFieldChoices($fieldConfig, $source['choices'], $locale);
        }
        if (isset($source['default'])) {
            $fieldConfig->setDefaultValue($source['default']);
        }
        if (isset($source['label_'.$locale])) {
            $fieldConfig->setLabel($source['label_'.$locale]);
        }
        if (isset($source['help_'.$locale])) {
            $fieldConfig->setHelp($source['help_'.$locale]);
        }
        if (isset($fieldType['class'])) {
            $fieldConfig->addClass($fieldType['class']);
        }

        $formConfig->addField($fieldConfig);
    }

    private function addFieldChoices(FieldConfig $fieldConfig, string $emsLink, string $locale)
    {
        $choices = $this->getSource($emsLink, ['values', 'labels_'.$locale]);
        $decoder = function (string $input) {
            return \json_decode($input, true);
        };

        $fieldConfig->setChoices(new FieldChoicesConfig(
            $choices['id'],
            $decoder($choices['values']),
            $decoder($choices['labels_'.$locale])
        ));
    }

    private function addFieldValidations(FieldConfig $fieldConfig, array $typeValidations = [], array $fieldValidations = []): void
    {
        $allValidations = array_merge($typeValidations, $fieldValidations);

        foreach ($allValidations as $v) {
            try {
                $validation = $this->getSource($v['validation'], ['classname', 'default_value']);
                $fieldConfig->addValidation(new ValidationConfig(
                    $validation['id'],
                    $validation['classname'],
                    ($validation['default_value'] ?? null),
                    ($v['value'] ?? null)
                ));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    private function addForm(FormConfig $formConfig, string $emsLinkForm, string $locale): void
    {
        $form = $this->getSource($emsLinkForm, ['fields']);

        foreach ($form['fields'] as $field) {
            try {
                $this->addField($formConfig, $field, $locale);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    private function getSource(string $emsLink, array $fields = []): array
    {
        $document = $this->client->getByEmsKey($emsLink, $fields);

        if (!$document) {
            throw new \LogicException(sprintf('Document type "%s" not found!', $emsLink));
        }

        $source = $document['_source'];
        $source['id'] = $document['_id'];

        return $source;
    }
}
