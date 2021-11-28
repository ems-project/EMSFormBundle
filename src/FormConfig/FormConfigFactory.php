<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Contracts\Elasticsearch\ClientRequestInterface;
use EMS\ClientHelperBundle\Contracts\Elasticsearch\ClientRequestManagerInterface;
use EMS\CommonBundle\Common\EMSLink;
use EMS\CommonBundle\Elasticsearch\Document\Document;
use EMS\CommonBundle\Json\JsonMenuNested;
use EMS\CommonBundle\Twig\TextRuntime;
use EMS\FormBundle\DependencyInjection\Configuration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class FormConfigFactory
{
    private ClientRequestInterface $client;
    private AdapterInterface $cache;
    private LoggerInterface $logger;
    /** @var array{name: string, cacheable: bool, domain: string, load-from-json: bool, submission-field: string, theme-field: string, form-template-field: string, form-field: string, type-form-choice: string, type-form-subform: string, type-form-markup: string, type-form-field: string, type: string} */
    private array $emsConfig;
    private bool $loadFromJson;
    private TextRuntime $textRuntime;

    /**
     * @param array{name: string, cacheable: bool, domain: string, load-from-json: bool, submission-field: string, theme-field: string, form-template-field: string, form-field: string, type-form-choice: string, type-form-subform: string, type-form-markup: string, type-form-field: string, type: string} $emsConfig
     */
    public function __construct(
        ClientRequestManagerInterface $manager,
        AdapterInterface $cache,
        LoggerInterface $logger,
        TextRuntime $textRuntime,
        array $emsConfig
    ) {
        $this->client = $manager->getDefault();
        $this->cache = $cache;
        $this->logger = $logger;
        $this->textRuntime = $textRuntime;
        $this->loadFromJson = $emsConfig[Configuration::LOAD_FROM_JSON];
        $this->emsConfig = $emsConfig;
    }

    public function create(string $ouuid, string $locale): FormConfig
    {
        $validityTags = $this->getValidityTags();
        $cacheKey = $this->client->getCacheKey(\sprintf('formconfig_%s_%s_', $ouuid, $locale));
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($this->emsConfig[Configuration::CACHEABLE] && $cacheItem->isHit()) {
            $data = $cacheItem->get();

            $cacheValidityTags = $data['validity_tags'] ?? null;
            $formConfig = $data['form_config'] ?? null;

            if ($formConfig && $cacheValidityTags === $validityTags) {
                return $formConfig;
            }
        }

        if ($this->loadFromJson) {
            $formConfig = $this->buildFromJson($ouuid, $locale);
        } else {
            $formConfig = $this->buildFromDocuments($ouuid, $locale);
        }

        $this->cache->save($cacheItem->set([
            'validity_tags' => $validityTags,
            'form_config' => $formConfig,
        ]));
        dump($formConfig);

        return $formConfig;
    }

    private function getValidityTags(): string
    {
        $validityTags = '';
        foreach ($this->emsConfig as $key => $value) {
            if ('type' !== \substr($key, 0, 4)) {
                continue;
            }

            if (\is_string($value) && null !== $contentType = $this->client->getContentType($value)) {
                $validityTags .= $contentType->getCacheValidityTag();
            }
        }

        return $validityTags;
    }

    private function buildFromDocuments(string $ouuid, string $locale): FormConfig
    {
        $source = $this->client->get($this->emsConfig[Configuration::TYPE], $ouuid)['_source'];
        $formConfig = new FormConfig($ouuid, $locale, $this->client->getCacheKey());

        if (isset($source[$this->emsConfig[Configuration::THEME_FIELD]])) {
            $formConfig->addTheme($source[$this->emsConfig[Configuration::THEME_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::FORM_TEMPLATE_FIELD]])) {
            $formConfig->setTemplate($source[$this->emsConfig[Configuration::FORM_TEMPLATE_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::DOMAIN_FIELD]])) {
            $this->addDomain($formConfig, $source[$this->emsConfig[Configuration::DOMAIN_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::SUBMISSION_FIELD]])) {
            $formConfig->setSubmissions($source[$this->emsConfig[Configuration::SUBMISSION_FIELD]]);
        }

        if (isset($source[$this->emsConfig[Configuration::FORM_FIELD]])) {
            $this->addForm($formConfig, $source[$this->emsConfig[Configuration::FORM_FIELD]], $locale);
        }

        return $formConfig;
    }

    private function buildFromJson(string $ouuid, string $locale): FormConfig
    {
        $source = $this->client->get($this->emsConfig[Configuration::TYPE], $ouuid)['_source'];
        $formConfig = new FormConfig($ouuid, $locale, $this->client->getCacheKey());
        if (isset($source[$this->emsConfig[Configuration::THEME_FIELD]])) {
            $formConfig->addTheme($source[$this->emsConfig[Configuration::THEME_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::FORM_TEMPLATE_FIELD]])) {
            $formConfig->setTemplate($source[$this->emsConfig[Configuration::FORM_TEMPLATE_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::DOMAIN_FIELD]])) {
            $this->addDomain($formConfig, $source[$this->emsConfig[Configuration::DOMAIN_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::NAME_FIELD]])) {
            $formConfig->setName($source[$this->emsConfig[Configuration::NAME_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::SUBMISSION_FIELD]])) {
            $this->loadJsonSubmissions($formConfig, $source[$this->emsConfig[Configuration::SUBMISSION_FIELD]]);
        }
        if (isset($source[$this->emsConfig[Configuration::FORM_FIELD]])) {
            $this->loadFormFromJson($formConfig, $source[$this->emsConfig[Configuration::FORM_FIELD]], $locale);
        }

        return $formConfig;
    }

    private function addDomain(FormConfig $formConfig, string $emsLinkDomain): void
    {
        $domain = $this->getDocument($emsLinkDomain, ['allowed_domains'])->getSource();
        $allowedDomains = $domain['allowed_domains'] ?? [];

        foreach ($allowedDomains as $allowedDomain) {
            $formConfig->addDomain($allowedDomain['domain']);
        }
    }

    private function addFieldChoices(FieldConfig $fieldConfig, string $emsLink, string $locale): void
    {
        $choices = $this->getDocument($emsLink, ['values', 'labels_'.$locale, 'choice_sort']);

        $decoder = function (string $input) {
            return \json_decode($input, true);
        };

        $source = $choices->getSource();
        $fieldChoicesConfig = new FieldChoicesConfig(
            $choices->getId(),
            $decoder($source['values']),
            $decoder($source['labels_'.$locale])
        );

        if (isset($source['choice_sort'])) {
            $fieldChoicesConfig->setSort($source['choice_sort']);
        }

        $fieldConfig->setChoices($fieldChoicesConfig);
    }

    /**
     * @param array<array> $typeValidations
     * @param array<array> $fieldValidations
     */
    private function addFieldValidations(FieldConfig $fieldConfig, array $typeValidations = [], array $fieldValidations = []): void
    {
        $allValidations = \array_merge($typeValidations, $fieldValidations);

        foreach ($allValidations as $v) {
            try {
                $validation = $this->getDocument($v['validation'], ['name', 'classname', 'default_value']);
                $fieldConfig->addValidation(new ValidationConfig(
                    $validation->getId(),
                    $validation->getSource()['name'],
                    $validation->getSource()['classname'],
                    $fieldConfig->getLabel(),
                    ($validation->getSource()['default_value'] ?? null),
                    ($v['value'] ?? null)
                ));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), [$e]);
            }
        }
    }

    private function addForm(FormConfig $formConfig, string $emsLinkForm, string $locale): void
    {
        $form = $this->getDocument($emsLinkForm, ['name', 'elements']);
        $formConfig->setName($form->getSource()['name']);
        $this->createElements($formConfig, $form->getSource()['elements'], $locale);
    }

    private function createElement(Document $element, string $locale, AbstractFormConfig $config): ElementInterface
    {
        switch ($element->getContentType()) {
            case $this->emsConfig['type-form-field']:
                return $this->createFieldConfig($element, $locale, $config);
            case $this->emsConfig['type-form-markup']:
                return new MarkupConfig($element->getId(), $element->getSource()['name'], $element->getSource()['markup_'.$locale]);
            case $this->emsConfig['type-form-subform']:
                return $this->createSubFormConfig($element, $locale, $config->getTranslationDomain());
        }

        throw new \RuntimeException(\sprintf('Implementation for configuration with name %s is missing', $element->getContentType()));
    }

    /** @param string[] $elementEmsLinks */
    private function createElements(AbstractFormConfig $config, array $elementEmsLinks, string $locale): void
    {
        $elements = $this->getElements($elementEmsLinks);

        foreach ($elements as $element) {
            try {
                $element = $this->createElement($element, $locale, $config);
                $config->addElement($element);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), [$e]);
            }
        }
    }

    private function createFieldConfig(Document $document, string $locale, AbstractFormConfig $config): FieldConfig
    {
        $source = $document->getSource();
        $fieldType = $this->getDocument($source['type'], ['name', 'class', 'classname', 'validations'])->getSource();
        $fieldConfig = new FieldConfig($document->getId(), $source['name'], $fieldType['name'], $fieldType['classname'], $config);

        if (isset($source['choices'])) {
            $this->addFieldChoices($fieldConfig, $source['choices'], $locale);
        }
        if (isset($source['default'])) {
            $fieldConfig->setDefaultValue($source['default']);
        }
        if (isset($source['placeholder_'.$locale])) {
            $fieldConfig->setPlaceholder($source['placeholder_'.$locale]);
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

        $this->addFieldValidations($fieldConfig, $fieldType['validations'] ?? [], $source['validations'] ?? []);

        return $fieldConfig;
    }

    private function createSubFormConfig(Document $document, string $locale, string $translationDomain): SubFormConfig
    {
        $source = $document->getSource();
        $subFormConfig = new SubFormConfig(
            $document->getId(),
            $locale,
            $translationDomain,
            $source['name'],
            $source['label_'.$locale]
        );
        $this->createElements($subFormConfig, $source['elements'], $locale);

        return $subFormConfig;
    }

    /** @param string[] $fields */
    private function getDocument(string $emsLink, array $fields = []): Document
    {
        $document = $this->client->getByEmsKey($emsLink, $fields);

        if (!$document) {
            throw new \LogicException(\sprintf('Document type "%s" not found!', $emsLink));
        }

        return Document::fromArray($document);
    }

    /**
     * @param string[] $emsLinks
     *
     * @return Document[]
     */
    private function getElements(array $emsLinks): array
    {
        $emsLinks = \array_map(fn ($emsLink) => EMSLink::fromText($emsLink), $emsLinks);

        $documentIds = \array_reduce($emsLinks, function (array $carry, EMSLink $emsLink) {
            $carry[$emsLink->getContentType()][] = $emsLink->getOuuid();

            return $carry;
        }, []);

        $documents = [];
        foreach ($documentIds as $contentType => $ouuids) {
            $search = $this->client->getByOuuids($contentType, $ouuids);
            $documents = \array_merge($documents, $search['hits']['hits'] ?? []);
        }

        $indexedDocuments = \array_reduce($documents, function (array $carry, array $hit) {
            $carry[$hit['_id']] = Document::fromArray($hit);

            return $carry;
        }, []);

        return \array_reduce($emsLinks, function (array $carry, EMSLink $emsLink) use ($indexedDocuments) {
            if ($indexedDocuments[$emsLink->getOuuid()]) {
                $carry[] = $indexedDocuments[$emsLink->getOuuid()];
            }

            return $carry;
        }, []);
    }

    private function loadJsonSubmissions(FormConfig $formConfig, string $submissionsJson): void
    {
        $submissions = $this->textRuntime->jsonMenuNestedDecode($submissionsJson);
        foreach ($submissions as $submission) {
            $formConfig->addSubmissions(new SubmissionConfig($submission->getObject()['type'], $submission->getObject()['endpoint'], $submission->getObject()['message']));
        }
    }

    private function loadFormFromJson(FormConfig $formConfig, string $json, string $locale): void
    {
        $config = $this->textRuntime->jsonMenuNestedDecode($json);
        foreach ($config as $element) {
            try {
                $element = $this->createElementFromJson($element, $locale, $formConfig);
                $formConfig->addElement($element);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), [$e]);
            }
        }
    }

    private function createElementFromJson(JsonMenuNested $element, string $locale, FormConfig $formConfig): FieldConfig
    {
        switch ($element->getType()) {
            case $this->emsConfig['type-form-field']:
                return $this->createFieldConfigFromJson($element, $locale, $formConfig);
        }

        throw new \RuntimeException(\sprintf('Implementation for configuration with name %s is missing', $element->getType()));
    }

    private function createFieldConfigFromJson(JsonMenuNested $document, string $locale, AbstractFormConfig $config): FieldConfig
    {
        $fieldConfig = new FieldConfig($document->getId(), $document->getObject()['name'], $document->getObject()['name'], $document->getObject()['classname'], $config);

        if (isset($document->getObject()['class'])) {
            $fieldConfig->addClass($document->getObject()['class']);
        }
        if (isset($document->getObject()['default'])) {
            $fieldConfig->setDefaultValue($document->getObject()['default']);
        }
        if (isset($document->getObject()[$locale]['placeholder'])) {
            $fieldConfig->setPlaceholder($document->getObject()[$locale]['placeholder']);
        }
        if (isset($document->getObject()[$locale]['label'])) {
            $fieldConfig->setLabel($document->getObject()[$locale]['label']);
        }
        if (isset($document->getObject()[$locale]['help'])) {
            $fieldConfig->setHelp($document->getObject()[$locale]['help']);
        }
        //TODO: add field choices
//        if (isset($source['choices'])) {
//            $this->addFieldChoices($fieldConfig, $source['choices'], $locale);
//        }
        //TODO: add field validations
//        $this->addFieldValidations($fieldConfig, $fieldType['validations'] ?? [], $source['validations'] ?? []);

        return $fieldConfig;
    }
}
