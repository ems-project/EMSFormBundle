<?php

namespace EMS\FormBundle\FormConfig;

class FormConfig extends AbstractFormConfig
{
    /** @var array */
    private $domains = [];
    /** @var array */
    private $themes = [];
    /** @var array */
    private $submissions = [];

    public function __construct(string $id, string $locale, string $translationDomain)
    {
        parent::__construct($id, $locale, $translationDomain);

        $this->themes[] = '@EMSForm/form_theme.html.twig';
    }

    public function addDomain(string $domain): void
    {
        $this->domains[] = $domain;
    }

    public function addTheme(string $theme): void
    {
        array_unshift($this->themes, $theme);
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function getSubmissions(): array
    {
        return $this->submissions;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function setSubmissions(array $submissions): void
    {
        $this->submissions = $submissions;
    }
}
