<?php

namespace EMS\FormBundle\FormConfig;

class ValidationConfig
{
    /** @var string */
    private $id;
    /** @var string */
    private $class;
    /** @var mixed */
    private $defaultValue;
    /** @var mixed */
    private $value;

    public function __construct(string $id, string $class, $defaultValue = null, $value = null)
    {
        if (!class_exists($class)) {
            throw new \Exception(sprintf('Error validation class "%s" does not exists!', $class));
        }

        $this->id = $id;
        $this->class = $class;
        $this->defaultValue = $defaultValue;
        $this->value = $value;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value ?? $this->defaultValue;
    }
}
