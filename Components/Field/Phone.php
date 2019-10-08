<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\TelType;

class Phone extends AbstractField
{
    public function getFieldClass(): string
    {
        return TelType::class;
    }

    public function getId(): string
    {
        return 'phone';
    }
}
