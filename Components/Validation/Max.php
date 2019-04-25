<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class Max extends Configuration
{
    public function getId(): string
    {
        return 'max';
    }

    public function build(): Constraint
    {
        return new LessThanOrEqual($this->value);
    }
}
