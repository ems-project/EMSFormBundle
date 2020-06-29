<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Validation\ValidationInterface;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\SubFormConfig;
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
            'label_attr' => $this->getLabelAttributes(),
            'required' => $this->isRequired(),
            'translation_domain' => false,
        ];
    }

    private function isRequired(): bool
    {
        foreach ($this->validations as $validation) {
            if ($validation->getHtml5AttributeName() === 'required') {
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
        return array_merge_recursive($this->getValidationHtml5Attribute(), [
            'class' => \implode(' ', [$this->getHtmlClass(), $this->config->getClass()]),
            'lang' => $this->config->getParentForm()->getLocale(),
        ]);
    }

    protected function getLabelAttributes(string $postfix = ''): array
    {
        $parentForm = $this->config->getParentForm();

        return [
            'id' => sprintf(
                'form_%s%s%s_label',
                $parentForm instanceof SubFormConfig ? sprintf('%s_', $parentForm->getName()) : '',
                $this->config->getName(),
                $postfix
            )
        ];
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
