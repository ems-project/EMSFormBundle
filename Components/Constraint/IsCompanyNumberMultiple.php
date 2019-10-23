<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

class IsCompanyNumberMultiple extends Constraint
{
    public $message = 'At least one company registration number "{{string}}" has an invalid format.';
}
