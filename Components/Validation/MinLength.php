<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;

class MinLength extends AbstractValidation
{
    public function getId(): string
    {
        return 'minlength';
    }

    public function getConstraint(): Constraint
    {
        return new Length(['min' => $this->value]);
    }
}
