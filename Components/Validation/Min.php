<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class Min extends Configuration
{
    public function getId(): string
    {
        return 'min';
    }

    public function build(): Constraint
    {
        return new GreaterThanOrEqual($this->value);
    }
}