<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;

class FormConfigFactory
{
    /** @var ClientRequest */
    private $client;
    /** @var array */
    private $emsFields;

    public function __construct(ClientRequestManager $manager, array $emsFields)
    {
        $this->client = $manager->getDefault();
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
            $formSource = $this->client->getByEmsKey($source[$this->emsFields['form-field']], ['fields'])['_source'];

            if (isset($formSource['fields'])) {
                foreach ($formSource['fields'] as $field) {
                    $this->addField($formConfig, $field, $locale);
                }
            }
        }

        return $formConfig;
    }

    private function addDomain(FormConfig $formConfig, string $emsLinkDomain): void
    {
        $domain = $this->client->getByEmsKey($emsLinkDomain, ['allowed_domains'])['_source'];
        $allowedDomains = $domain['allowed_domains'] ?? [];

        foreach ($allowedDomains as $allowedDomain) {
            $formConfig->addDomain($allowedDomain['domain']);
        }
    }

    private function addField(FormConfig $formConfig, array $source, string $locale): void
    {
        $type = $this->client->getByEmsKey($source['type'], ['name', 'classname', 'validations'])['_source'];
        $fieldConfig = new FieldConfig($source['technical_name'], $type['name'], $type['classname']);

        $validations = array_merge($type['validations'] ?? [], $source['validations'] ?? []);

        foreach ($validations as $v) {
            $validation = $this->client->getByEmsKey($v['validation'], ['classname', 'default_value', 'name'])['_source'];

            $value = $v['value'] ?? null;
            $defaultValue = $validation['default_value'] ?? null;

            $fieldConfig->addValidation(new ValidationConfig($validation['name'], $validation['classname'], $defaultValue, $value));
        }

        if (isset($source['label_'.$locale])) {
            $fieldConfig->setLabel($source['label_'.$locale]);
        }
        if (isset($source['help_'.$locale])) {
            $fieldConfig->setHelp($source['help_'.$locale]);
        }

        $formConfig->addField($fieldConfig);
    }
}
