<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;

class MinLength extends Configuration
{

    public function getId(): string
    {
        return 'minlength';
    }

    public function build(): Constraint
    {
        return new Length(['min' => $this->value]);
    }
}