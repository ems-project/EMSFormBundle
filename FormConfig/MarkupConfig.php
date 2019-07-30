<?php

namespace EMS\FormBundle\FormConfig;

use EMS\FormBundle\Components\Form\MarkupType;

class MarkupConfig implements ElementInterface
{
    /** @var string */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $markup;

    public function __construct(string $id, string $name, string $markup)
    {
        $this->id = $id;
        $this->name = $name;
        $this->markup = $markup;
    }

    public function getClassName(): string
    {
        return MarkupType::class;
    }

    public function getMarkup(): string
    {
        return $this->markup;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
