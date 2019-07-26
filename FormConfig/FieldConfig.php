<?php

namespace EMS\FormBundle\FormConfig;

class FieldConfig
{
    /** @var string */
    private $id;
    /** @var string */
    private $type;
    /** @var array */
    private $class = [];
    /** @var string */
    private $className;
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

    public function __construct(string $id, string $type, string $className)
    {
        if (!class_exists($className)) {
            throw new \Exception(sprintf('Error field class "%s" does not exists!', $className));
        }

        $this->id = $id;
        $this->type = $type;
        $this->className = $className;
        $this->class[] = $id;
    }

    public function addClass(string $class): void
    {
        $this->class[] = $class;
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
        return implode(' ', $this->class);
    }

    public function getClassName(): string
    {
        return $this->className;
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

    public function getId(): string
    {
        return $this->id;
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
