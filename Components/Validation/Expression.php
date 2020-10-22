<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsExpression;
use Symfony\Component\Validator\Constraint;

final class Expression extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new IsExpression(['expression' => $this->value]);
    }
}
