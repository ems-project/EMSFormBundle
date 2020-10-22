<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

final class IsBelgiumPhoneNumber extends Constraint
{
    public $message = 'The phone number "{{string}}" is invalid.';
}
