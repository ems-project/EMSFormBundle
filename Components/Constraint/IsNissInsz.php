<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsNissInsz extends Constraint
{
    public $message = 'The social security number "{{ string }}" has an invalid format.';
}