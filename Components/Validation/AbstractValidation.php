<?php

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\FormConfig\ValidationConfig;

abstract class AbstractValidation implements ValidationInterface
{
    protected $value;

    public function __construct(ValidationConfig $config)
    {
        $this->value = $config->getValue();
    }

    public function getHtml5Attribute(): array
    {
        return [$this->getId() => $this->value];
    }
}
