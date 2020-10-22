<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsVerificationCode;
use Symfony\Component\Validator\Constraint;

final class VerificationCode extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new IsVerificationCode(['field' => $this->getField()]);
    }

    public function getField(): string
    {
        return $this->value;
    }

    public function getHtml5Attribute(): array
    {
        return [];
    }
}
