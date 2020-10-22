<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsBelgiumPhoneNumber;
use Symfony\Component\Validator\Constraint;

final class Phone extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new IsBelgiumPhoneNumber($this->value);
    }

    public function getHtml5Attribute(): array
    {
        return [];
    }
}
