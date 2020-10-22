<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

final class Max extends AbstractValidation
{
    public function getHtml5AttributeName(): string
    {
        return 'max';
    }

    public function getConstraint(): Constraint
    {
        return new LessThanOrEqual($this->value);
    }
}
