<?php

namespace EMS\FormBundle\FormConfig;

use EMS\ClientHelperBundle\Helper\Twig\TwigLoader;

class FormConfig extends AbstractFormConfig
{
    /** @var array */
    private $domains = [];
    /** @var string */
    private $template;
    /** @var array */
    private $themes = [];
    /** @var array */
    private $submissions = [];

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

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function setSubmissions(array $submissions): void
    {
        $this->submissions = $submissions;
    }

    public function setTemplate(string $template): void
    {
        $this->template = TwigLoader::PREFIX . '/' . $template;
    }
}
