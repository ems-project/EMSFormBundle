<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class Checkbox extends AbstractField
{
    public function getFieldClass(): string
    {
        return CheckboxType::class;
    }

    public function getId(): string
    {
        return 'checkbox';
    }
}
