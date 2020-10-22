<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsCompanyNumberMultiple;
use Symfony\Component\Validator\Constraint;

final class CompanyNumberMultiple extends AbstractValidation
{
    public function getConstraint(): Constraint
    {
        return new IsCompanyNumberMultiple($this->value);
    }

    public function getHtml5Attribute(): array
    {
        return [];
    }
}
