<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsNissInsz;
use Symfony\Component\Validator\Constraint;

final class NissInsz extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new IsNissInsz($this->value);
    }

    public function getHtml5Attribute(): array
    {
        return [];
    }
}
