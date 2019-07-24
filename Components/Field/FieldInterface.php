<?php

namespace EMS\FormBundle\Components\Field;

interface FieldInterface
{
    public function getId(): string;

    public function getFieldClass(): string;

    public function getOptions(): array;
}