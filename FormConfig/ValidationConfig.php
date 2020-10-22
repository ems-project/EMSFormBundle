<?php

declare(strict_types=1);

namespace EMS\FormBundle\FormConfig;

final class ValidationConfig
{
    /** @var string */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $className;
    /** @var mixed */
    private $defaultValue;
    /** @var mixed */
    private $value;

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

    public function getValue()
    {
        return $this->value ?? $this->defaultValue;
    }
}
