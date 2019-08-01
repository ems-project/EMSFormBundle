<?php

namespace EMS\FormBundle\Components\Validation;

class MaxLengthCounter extends MaxLength
{
    public function getId(): string
    {
        return 'maxlength_counter';
    }

    public function getHtml5Attribute(): array
    {
        return [
            'maxlength' => $this->value,
            'class' => ['counter']
        ];
    }
}
