<?php

namespace EMS\FormBundle\FormConfig;

abstract class AbstractFormConfig
{
    /** @var string */
    private $id;
    /** @var string */
    private $name;
    /** @var ElementInterface[] */
    private $elements = [];

    public function __construct(string $id, string $name = '')
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ElementInterface[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function addElement(ElementInterface $element): void
    {
        $this->elements[$element->getName()] = $element;
    }
}
