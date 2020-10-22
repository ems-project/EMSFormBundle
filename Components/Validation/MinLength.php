<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;

final class MinLength extends AbstractValidation
{
    public function getHtml5AttributeName(): string
    {
        return 'minlength';
    }

    public function getConstraint(): Constraint
    {
        return new Length(['min' => $this->value]);
    }
}
