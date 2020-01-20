<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;

class FileMimeTypes extends AbstractValidation
{
    public function getHtml5AttributeName(): string
    {
        return 'accept';
    }

    public function getConstraint(): Constraint
    {
        if (strpos($this->value, ',') !== false) {
            return new File(['mimeTypes' => explode(',', $this->value)]);
        }

        return new File(['mimeTypes' => $this->value]);
    }
}
