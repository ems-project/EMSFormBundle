<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Validation\ValidationInterface;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\ValidationConfig;

abstract class AbstractField implements FieldInterface
{
    /** @var FieldConfig */
    private $config;
    /** @var ValidationInterface[] */
    private $validations = [];

    public function __construct(FieldConfig $config)
    {
        $this->config = $config;

        foreach ($config->getValidations() as $validationConfig) {
            $validation = $this->createValidation($validationConfig);
            $this->validations[$validation->getId()] = $validation;
        }
    }

    public function getOptions(): array
    {
        return [
            'required' => $this->isRequired(),
            'label' => $this->config->getLabel(),
            'help' => $this->config->getHelp(),
            'attr' => $this->getAttributes(),
            'constraints' => $this->getValidationConstraints(),
        ];
    }

    private function isRequired(): bool
    {
        return array_key_exists('required', $this->validations);
    }

    private function createValidation(ValidationConfig $config): ValidationInterface
    {
        $class = $config->getClass();
        return new $class($config);
    }

    private function getAttributes(): array
    {
        return array_merge($this->getValidationHtml5Attribute(), ['class' => $this->getId()]);
    }

    private function getValidationConstraints(): array
    {
        return array_map(function (ValidationInterface $validation) {
            return $validation->getConstraint();
        }, $this->validations);
    }

    private function getValidationHtml5Attribute(): array
    {
        $html5Attributes = [];

        foreach ($this->validations as $validation) {
            $html5Attributes = array_merge($html5Attributes, $validation->getHtml5Attribute());
        }

        return $html5Attributes;
    }
}
