<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use EMS\FormBundle\Components\ValueObject\BelgiumCompanyNumberMultiple;

class CompanyNumberMultiple extends AbstractForgivingNumberField
{
    public function getFieldClass(): string
    {
        return TextareaType::class;
    }

    public function getId(): string
    {
        return 'company-number-multiple';
    }
    
    public function getValueObjects() : array
    {
        return [BelgiumCompanyNumberMultiple::class];
    }
}
