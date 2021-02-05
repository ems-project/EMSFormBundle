<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;
use EMS\CommonBundle\Common\EMSLink;
use EMS\CommonBundle\Elasticsearch\Document\Document;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class FormConfigFactory
{
    private ClientRequest $client;
    private AdapterInterface $cache;
    private LoggerInterface $logger;
    /** @var array<string, string> */
    private array $emsConfig;

    public function __construct(
        ClientRequestManager $manager,
        AdapterInterface $cache,
        LoggerInterface $logger,
        array $emsConfig
    ) {
        $this->client = $manager->getDefault();
        $this->cache = $cache;
        $this->logger = $logger;
        $this->emsConfig = $emsConfig;
    }

    public function create(string $ouuid, string $locale): FormConfig
    {
        $validityTags = $this->getValidityTags();
        $cacheKey = $this->client->getCacheKey(\sprintf('formconfig_%s_%s_', $ouuid, $locale));
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            $data = $cacheItem->get();

            $cacheValidityTags = $data['validity_tags'] ?? null;
            $formConfig = $data['form_config'] ?? null;

            if ($formConfig && $cacheValidityTags === $validityTags) {
                return $formConfig;
            }
        }

        $formConfig = $this->build($ouuid, $locale);

        $this->cache->save($cacheItem->set([
            'validity_tags' => $validityTags,
            'form_config' => $formConfig,
        ]));

        return $formConfig;
    }

    private function getValidityTags(): string
    {
        $validityTags = '';
        foreach ($this->emsConfig as $key => $value) {
            if ('type' !== \substr($key, 0, 4)) {
                continue;
            }

            if (null !== $contentType = $this->client->getContentType($value)) {
                $validityTags .= $contentType->getCacheValidityTag();
            }
        }

        return $validityTags;
    }

    private function build(string $ouuid, string $locale): FormConfig
    {
        $source = $this->client->get($this->emsConfig['type'], $ouuid)['_source'];
        $formConfig = new FormConfig($ouuid, $locale, $this->client->getCacheKey());

        if (isset($source[$this->emsConfig['theme-field']])) {
            $formConfig->addTheme($source[$this->emsConfig['theme-field']]);
        }
        if (isset($source[$this->emsConfig['form-template-field']])) {
            $formConfig->setTemplate($source[$this->emsConfig['form-template-field']]);
        }
        if (isset($source['domain'])) {
            $this->addDomain($formConfig, $source['domain']);
        }
        if (isset($source[$this->emsConfig['submission-field']])) {
            $formConfig->setSubmissions($source[$this->emsConfig['submission-field']]);
        }

        if (isset($source[$this->emsConfig['form-field']])) {
            $this->addForm($formConfig, $source[$this->emsConfig['form-field']], $locale);
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
    }

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
}
