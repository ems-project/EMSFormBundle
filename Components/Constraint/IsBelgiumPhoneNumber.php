<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

class IsBelgiumPhoneNumber extends Constraint
{
    public $message = 'The phone number "{{string}}" is invalid.';
}
