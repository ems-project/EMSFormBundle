<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

final class IsCompanyNumber extends Constraint
{
    public $message = 'The company registration number "{{string}}" is invalid.';
}
