<?php

namespace EMS\FormBundle\FormConfig;

class FormConfig
{
    /** @var string */
    private $name;
    /** @var string */
    private $locale;
    /** @var string */
    private $translationDomain;
    /** @var array */
    private $domains = [];
    /** @var FieldConfig[] */
    private $fields = [];
    /** @var string */
    private $theme;

    public function __construct(string $name, string $locale, string $translationDomain)
    {
        $this->name = $name;
        $this->locale = $locale;
        $this->translationDomain = $translationDomain;
    }

    public function addDomain(string $domain): void
    {
        $this->domains[] = $domain;
    }

    public function addField(FieldConfig $field): void
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * @return FieldConfig[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function isAllowedDomain(string $domain): bool
    {
        return \in_array($domain, $this->domains, true);
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }
}