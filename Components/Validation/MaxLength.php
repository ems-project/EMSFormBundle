<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;

final class MaxLength extends AbstractValidation
{
    public function getHtml5AttributeName(): string
    {
        return 'maxlength';
    }

    public function getConstraint(): Constraint
    {
        return new Length(['max' => $this->value]);
    }
}
