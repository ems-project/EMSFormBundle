<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;

class FormConfigFactory
{
    /** @var ClientRequest */
    private $client;

    public function __construct(ClientRequestManager $manager)
    {
        $this->client = $manager->getDefault();
    }

    public function create(string $ouuid, string $locale): FormConfig
    {
        $source = $this->client->get('form_instance', $ouuid)['_source'];
        $formConfig = new FormConfig($ouuid, $locale, $this->client->getCacheKey());

        if (isset($source['theme_template'])) {
            $formConfig->setTheme($source['theme_template']);
        }
        if (isset($source['domain'])) {
            $this->addDomain($formConfig, $source['domain']);
        }

        $formSource = isset($source['form']) ? $this->client->getByEmsKey($source['form'], ['fields'])['_source'] : false;

        if ($formSource && isset($formSource['fields'])) {
            foreach ($formSource['fields'] as $field) {
                $this->addField($formConfig, $field, $locale);
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

        foreach($validations as $v) {
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