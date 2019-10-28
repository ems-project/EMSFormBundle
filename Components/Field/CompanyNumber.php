<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BelgiumCompanyNumber;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CompanyNumber extends AbstractForgivingNumberField
{
    public function getFieldClass(): string
    {
        return TextType::class;
    }

    public function getId(): string
    {
        return 'company-number';
    }
    
    public function getValueObjects(): array
    {
        return [BelgiumCompanyNumber::class];
    }
}
