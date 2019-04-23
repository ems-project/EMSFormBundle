<?php

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsNissInsz;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;

class NissInsz extends Configuration
{

    public function getId(): string
    {
        return 'niss-insz';
    }

    public function build(): Constraint
    {
        return new IsNissInsz($this->value);
    }
}