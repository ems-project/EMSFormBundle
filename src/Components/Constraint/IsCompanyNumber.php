<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

class IsCompanyNumber extends Constraint
{
    public $message = 'The company registration number "{{string}}" is invalid.';
}
