<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class Max extends AbstractValidation
{
    public function getId(): string
    {
        return 'max';
    }

    public function getConstraint(): Constraint
    {
        return new LessThanOrEqual($this->value);
    }
}
