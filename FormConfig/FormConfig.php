<?php

namespace EMS\FormBundle\FormConfig;

use EMS\SubmissionBundle\FormConfig\SubmissionConfig;

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
    /** @var ?string */
    private $theme;
    /** @var array */
    private $submissions;

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

    public function addSubmission(SubmissionConfig $submission): void
    {
        $this->submissions[] = $submission;
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

    public function getSubmissions(): array
    {
        return $this->submissions;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function setSubmissions(array $submissions): void
    {
        $this->submissions = $submissions;
    }
}
