<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;

abstract class Configuration
{
    /** @var mixed */
    protected $value;

    public function __construct(array $validationDefinition)
    {
        $this->value = $validationDefinition['value'] ?? ($validationDefinition['validation']['_source']['default_value'] ?? null);
    }

    /**
     * return an empty array in concrete classes that are not implementing HTML5 validations
     */
    public function getHtml5Attribute(): array
    {
        return [$this->getId() => $this->value];
    }

    abstract public function getId(): string;

    abstract public function build(): Constraint;
}
