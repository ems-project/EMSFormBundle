<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Contracts\Templating\TemplatingInterface;

class FormConfig extends AbstractFormConfig
{
    /** @var string[] */
    private array $domains = [];
    private string $template;
    /** @var string[] */
    private array $themes = [];
    /** @var array<SubmissionConfig|string> */
    private array $submissions = [];

    public function __construct(string $id, string $locale, string $translationDomain)
    {
        parent::__construct($id, $locale, $translationDomain);

        $this->template = '@EMSForm/form.html.twig';
        $this->themes[] = '@EMSForm/form_theme.html.twig';
    }

    public function addDomain(string $domain): void
    {
        $this->domains[] = $domain;
    }

    public function addTheme(string $theme): void
    {
        \array_unshift($this->themes, $theme);
    }

    /** @return string[] */
    public function getDomains(): array
    {
        return $this->domains;
    }

    /** @return array<SubmissionConfig|string> */
    public function getSubmissions(): array
    {
        return $this->submissions;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /** @return string[] */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /** @param array<SubmissionConfig|string> $submissions */
    public function setSubmissions(array $submissions): void
    {
        $this->submissions = $submissions;
    }

    public function setTemplate(string $template): void
    {
        $this->template = TemplatingInterface::PREFIX.'/'.$template;
    }
}
