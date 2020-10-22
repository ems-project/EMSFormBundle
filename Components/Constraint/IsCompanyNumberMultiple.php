<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

final class IsCompanyNumberMultiple extends Constraint
{
    public $message = 'At least one company registration number "{{string}}" is invalid.';
}
