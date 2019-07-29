<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequest;
use EMS\ClientHelperBundle\Helper\Elasticsearch\ClientRequestManager;
use EMS\CommonBundle\Common\Document;
use EMS\CommonBundle\Common\EMSLink;
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
            $formConfig->setTheme($source[$this->emsFields['theme-field']]);
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
        $domain = $this->getDocument($emsLinkDomain, ['allowed_domains'])->getSource();
        $allowedDomains = $domain['allowed_domains'] ?? [];

        foreach ($allowedDomains as $allowedDomain) {
            $formConfig->addDomain($allowedDomain['domain']);
        }
    }

    private function addField(FormConfig $formConfig, Document $document, string $locale): void
    {
        $source = $document->getSource();
        $fieldType = $this->getDocument($source['type'], ['name', 'class', 'classname', 'validations'])->getSource();
        $fieldConfig = new FieldConfig($document->getOuuid(), $source['name'], $fieldType['name'], $fieldType['classname']);

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
        $choices = $this->getDocument($emsLink, ['values', 'labels_'.$locale]);

        $decoder = function (string $input) {
            return \json_decode($input, true);
        };

        $fieldConfig->setChoices(new FieldChoicesConfig(
            $choices->getOuuid(),
            $decoder($choices->getSource()['values']),
            $decoder($choices->getSource()['labels_'.$locale])
        ));
    }

    private function addFieldValidations(FieldConfig $fieldConfig, array $typeValidations = [], array $fieldValidations = []): void
    {
        $allValidations = array_merge($typeValidations, $fieldValidations);

        foreach ($allValidations as $v) {
            try {
                $validation = $this->getDocument($v['validation'], ['name', 'classname', 'default_value']);
                $fieldConfig->addValidation(new ValidationConfig(
                    $validation->getOuuid(),
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
        $form = $this->getDocument($emsLinkForm, ['elements']);
        $elements = $this->getElements($form->getSource()['elements']);

        foreach ($elements as $element) {
            try {
                $this->addField($formConfig, $element, $locale);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), [$e]);
            }
        }
    }

    private function getDocument(string $emsLink, array $fields = []): Document
    {
        $document = $this->client->getByEmsKey($emsLink, $fields);

        if (!$document) {
            throw new \LogicException(sprintf('Document type "%s" not found!', $emsLink));
        }

        return new Document($document['_type'], $document['_id'], $document['_source']);
    }

    /**
     * @param string[] $emsLinks
     *
     * @return Document[]
     */
    private function getElements(array $emsLinks): array
    {
        $emsLinks = array_map(function (string $emsLink) { return EMSLink::fromText($emsLink);}, $emsLinks);
        $types = array_unique(array_map(function (EMSLink $emsLink) { return $emsLink->getContentType(); }, $emsLinks));

        $search = $this->client->search($types, [
            'size' => \count($emsLinks),
            'query' => [
                'terms' => [
                    '_id' => array_map(function (EMSLink $emsLink) { return $emsLink->getOuuid(); }, $emsLinks)
                ]
            ]
        ])['hits']['hits'];

        return array_filter(array_map(function (EMSLink $emsLink) use ($search) {
            foreach ($search as $hit) {
                if ($hit['_id'] === $emsLink->getOuuid()) {
                    return new Document($hit['_type'], $hit['_id'], $hit['_source']);
                }
            }

            return null;
        }, $emsLinks));
    }
}
