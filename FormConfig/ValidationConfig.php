<?php

namespace EMS\FormBundle\FormConfig;

class ValidationConfig
{
    /** @var string */
    private $name;
    /** @var string */
    private $class;
    /** @var mixed */
    private $defaultValue;
    /** @var mixed */
    private $value;

    public function __construct(string $name, string $class, $defaultValue = null, $value = null)
    {
        $this->name = $name;
        $this->class = $class;
        $this->defaultValue = $defaultValue;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getValue()
    {
        return $this->value ?? $this->defaultValue;
    }
}
