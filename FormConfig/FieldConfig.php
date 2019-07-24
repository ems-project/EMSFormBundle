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
    private $label;
    /** @var ?string */
    private $help;
    /** @var ValidationConfig[] */
    private $validations = [];

    public function __construct(string $name, string $type, string $class)
    {
        $this->name = $name;
        $this->type = $type;
        $this->class = $class;
    }

    public function addValidation(ValidationConfig $validation)
    {
        $this->validations[$validation->getName()] = $validation;
    }

    /**
     * @return ValidationConfig[]
     */
    public function getValidations(): array
    {
        return $this->validations;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setLabel($label): void
    {
        $this->label = $label;
    }

    public function setHelp($help): void
    {
        $this->help = $help;
    }
}