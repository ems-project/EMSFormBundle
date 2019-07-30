<?php

namespace EMS\FormBundle\Components\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class MarkupType extends AbstractType
{
    public function getParent()
    {
        return FormType::class;
    }

    public function getBlockPrefix()
    {
        return 'ems_markup';
    }
}
