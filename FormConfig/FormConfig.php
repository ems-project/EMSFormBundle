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
    /** @var ElementInterface[] */
    private $elements = [];
    /** @var array */
    private $themes = [];
    /** @var array */
    private $submissions = [];

    public function __construct(string $id, string $locale, string $translationDomain)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->translationDomain = $translationDomain;

        $this->themes[] = '@EMSForm/form_theme.html.twig';
    }

    public function addDomain(string $domain): void
    {
        $this->domains[] = $domain;
    }

    public function addElement(ElementInterface $element): void
    {
        $this->elements[$element->getName()] = $element;
    }

    public function addTheme(string $theme): void
    {
        array_unshift($this->themes, $theme);
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
     * @return ElementInterface[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function getSubmissions(): array
    {
        return $this->submissions;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setSubmissions(array $submissions): void
    {
        $this->submissions = $submissions;
    }
}
