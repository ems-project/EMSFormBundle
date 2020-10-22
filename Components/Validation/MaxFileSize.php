<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;

final class MaxFileSize extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new File(['maxSize' => $this->value]);
    }
}
