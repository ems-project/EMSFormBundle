<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BelgiumPhoneNumber;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class Phone extends AbstractForgivingNumberField
{
    public function getFieldClass(): string
    {
        return TelType::class;
    }

    public function getId(): string
    {
        return 'phone';
    }
    
    public function getTransformerClasses(): array
    {
        return [BelgiumPhoneNumber::class];
    }
}
