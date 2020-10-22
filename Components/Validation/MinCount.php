<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

final class MinCount extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new Constraints\Count(['min' => $this->value]);
    }
}
