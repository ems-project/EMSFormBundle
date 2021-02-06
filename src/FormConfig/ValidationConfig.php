<?php

namespace EMS\FormBundle\FormConfig;

class ValidationConfig
{
    private string $id;
    private string $name;
    private string $className;
    /** @var mixed */
    private $defaultValue;
    /** @var mixed */
    private $value;

    /**
     * @param mixed $defaultValue
     * @param mixed $value
     */
    public function __construct(string $id, string $name, string $className, $defaultValue = null, $value = null)
    {
        if (!\class_exists($className)) {
            throw new \Exception(\sprintf('Error validation class "%s" does not exists!', $className));
        }

        $this->id = $id;
        $this->name = $name;
        $this->className = $className;
        $this->defaultValue = $defaultValue;
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return mixed|null */
    public function getValue()
    {
        return $this->value ?? $this->defaultValue;
    }
}
