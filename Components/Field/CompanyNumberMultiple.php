<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CompanyNumberMultiple extends AbstractField
{
    public function getFieldClass(): string
    {
        return TextareaType::class;
    }

    public function getId(): string
    {
        return 'company-number-multiple';
    }
}
