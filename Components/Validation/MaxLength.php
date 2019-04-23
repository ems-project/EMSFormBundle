<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;

class MaxLength extends Configuration
{

    public function getId(): string
    {
        return 'maxlength';
    }

    public function build(): Constraint
    {
        return new Length(['max' => $this->value]);
    }
}