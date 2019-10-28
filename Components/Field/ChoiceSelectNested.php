<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Form\NestedChoiceType;

class ChoiceSelectNested extends AbstractField
{
    public function getFieldClass(): string
    {
        return NestedChoiceType::class;
    }

    public function getId(): string
    {
        return 'choice_select_nested';
    }
}
