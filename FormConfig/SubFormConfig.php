<?php

namespace EMS\FormBundle\FormConfig;

use EMS\FormBundle\Components\Form\SubFormType;

class SubFormConfig extends AbstractFormConfig implements ElementInterface
{
    /** @var string */
    private $label;

    public function __construct(string $id, string $name, string $label)
    {
        parent::__construct($id, $name);
        $this->label = $label;
    }

    public function getClassName(): string
    {
        return SubFormType::class;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
