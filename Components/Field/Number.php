<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

class Number extends Configuration
{
    protected function getFieldClass(): string
    {
        return NumberType::class;
    }

    public function getId(): string
    {
        return 'number';
    }
}