<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\NumberValue;

final class NumberForgivingInput extends AbstractForgivingNumberField
{
    public function getHtmlClass(): string
    {
        return 'number-forgiving-input';
    }

    public function getTransformerClasses(): array
    {
        return [NumberValue::class];
    }
}
