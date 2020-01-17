<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;

class MaxFileSize extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new File(['maxSize' => $this->value]);
    }
}
