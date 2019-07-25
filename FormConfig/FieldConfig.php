<?php

namespace EMS\FormBundle\FormConfig;

class FieldConfig
{
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var string */
    private $class;
    /** @var ?string */
    private $defaultValue;
    /** @var ?string */
    private $label;
    /** @var ?string */
    private $help;
    /** @var ValidationConfig[] */
    private $validations = [];
    /** @var FieldChoicesConfig */
    private $choices;

    public function __construct(string $name, string $type, string $class)
    {
        if (!class_exists($class)) {
            throw new \Exception(sprintf('Error field class "%s" does not exists!', $class));
        }

        $this->name = $name;
        $this->type = $type;
        $this->class = $class;
    }

    public function addValidation(ValidationConfig $validation)
    {
        $this->validations[$validation->getId()] = $validation;
    }

    public function getChoices(): FieldChoicesConfig
    {
        return $this->choices;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ValidationConfig[]
     */
    public function getValidations(): array
    {
        return $this->validations;
    }

    public function setChoices(FieldChoicesConfig $choices): void
    {
        $this->choices = $choices;
    }

    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function setHelp($help): void
    {
        $this->help = $help;
    }

    public function setLabel($label): void
    {
        $this->label = $label;
    }
}
