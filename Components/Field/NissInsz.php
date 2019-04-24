<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class NissInsz extends Configuration
{
    protected function getFieldClass(): string
    {
        return TextType::class;
    }

    public function getId(): string
    {
        return 'niss-insz';
    }
}
