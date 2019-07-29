<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Validation\ValidationInterface;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\ValidationConfig;

abstract class AbstractField implements FieldInterface
{
    /** @var FieldConfig */
    protected $config;
    /** @var ValidationInterface[] */
    private $validations = [];

    public function __construct(FieldConfig $config)
    {
        $this->config = $config;

        foreach ($config->getValidations() as $id => $validationConfig) {
            $this->validations[$id] = $this->createValidation($validationConfig);
        }
    }

    public function getOptions(): array
    {
        return [
            'attr' => $this->getAttributes(),
            'constraints' => $this->getValidationConstraints(),
            'data' => $this->config->getDefaultValue(),
            'help' => $this->config->getHelp(),
            'label' => $this->config->getLabel(),
            'required' => $this->isRequired(),
            'translation_domain' => false,
        ];
    }

    private function isRequired(): bool
    {
        foreach ($this->validations as $validation) {
            if ($validation->getId() === 'required') {
                return true;
            }
        }

        return false;
    }

    private function createValidation(ValidationConfig $config): ValidationInterface
    {
        $class = $config->getClassName();
        return new $class($config);
    }

    protected function getAttributes(): array
    {
        return array_merge($this->getValidationHtml5Attribute(), ['class' => $this->config->getClass()]);
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
