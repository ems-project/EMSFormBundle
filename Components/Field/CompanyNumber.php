<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BelgiumCompanyNumber;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CompanyNumber extends AbstractForgivingNumberField
{
    public function getHtmlClass(): string
    {
        return 'company-number';
    }
    
    public function getTransformerClasses(): array
    {
        return [BelgiumCompanyNumber::class];
    }
}
