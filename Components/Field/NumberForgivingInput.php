<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\NumberValue;

class NumberForgivingInput extends AbstractForgivingNumberField
{
    public function getId(): string
    {
        return 'number-forgiving-input';
    }
    
    public function getTransformerClasses(): array
    {
        return [NumberValue::class];
    }
}
