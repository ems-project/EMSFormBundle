<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

final class MaxLengthCounter extends MaxLength
{
    public function getHtml5Attribute(): array
    {
        return [
            'maxlength' => $this->value,
            'class' => ['counter'],
        ];
    }
}
