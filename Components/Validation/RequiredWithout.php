<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsRequiredWithout;
use Symfony\Component\Validator\Constraint;

final class RequiredWithout extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new IsRequiredWithout(['otherField' => $this->value]);
    }
}
