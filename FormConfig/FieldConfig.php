<?php

namespace EMS\FormBundle\FormConfig;

class FieldConfig implements ElementInterface
{
    /** @var string */
    private $id;
    /** @var string */
    private $name;
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
    /** @var ?FieldChoicesConfig */
    private $choices;
    /** @var AbstractFormConfig */
    private $parentForm;

    public function __construct(string $id, string $name, string $type, string $className, AbstractFormConfig $parentForm)
    {
        if (!\class_exists($className)) {
            throw new \Exception(\sprintf('Error field class "%s" does not exists!', $className));
        }

        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->className = $className;
        $this->class[] = $name;
        $this->parentForm = $parentForm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addClass(string $class): void
    {
        $this->class[] = $class;
    }

    public function addValidation(ValidationConfig $validation)
    {
        $this->validations[$validation->getName()] = $validation;
    }

    public function hasChoices(): bool
    {
        return ($this->choices instanceof FieldChoicesConfig) && (\count($this->choices->list()) > 0);
    }

    public function getChoicePlaceholder(): ?string
    {
        return $this->choices ? $this->choices->getPlaceHolder() : null;
    }

    public function getChoiceList(): array
    {
        return $this->choices ? $this->choices->list() : [];
    }

    public function getChoices(): ?FieldChoicesConfig
    {
        return $this->choices;
    }

    public function getClass(): string
    {
        $classes = $this->class;
        if (null !== $this->getChoices() && $this->getChoices()->isMultiLevel()) {
            $classes[] = 'dynamic-choice-select';
        }

        return \implode(' ', $classes);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $classname): void
    {
        $this->className = $classname;
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

    public function getParentForm(): AbstractFormConfig
    {
        return $this->parentForm;
    }
}
