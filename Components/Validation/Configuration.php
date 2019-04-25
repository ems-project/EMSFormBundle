<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;

abstract class Configuration
{
    /** @var mixed */
    protected $value;

    /** @var string */
    protected $type;

    public function __construct(array $validationDefinition)
    {
        $this->value = $validationDefinition['value'] ?? ($validationDefinition['validation']['_source']['default_value'] ?? null);
        $this->type = $validationDefinition['validation']['_source']['type'];
    }

    public function getHtml5Attribute(): array
    {
        return $this->type === 'html5' ? [$this->getId() => $this->value] : [];
    }

    abstract public function getId(): string;

    abstract public function build(): Constraint;
}
