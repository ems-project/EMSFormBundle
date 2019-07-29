<?php

namespace EMS\FormBundle\FormConfig;

class FormConfig
{
    /** @var string */
    private $id;
    /** @var string */
    private $locale;
    /** @var string */
    private $translationDomain;
    /** @var array */
    private $domains = [];
    /** @var FieldConfig[] */
    private $fields = [];
    /** @var ?string */
    private $theme;

    public function __construct(string $id, string $locale, string $translationDomain)
    {
        $this->id = $id;
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

    public function getDomains(): array
    {
        return $this->domains;
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }
}
