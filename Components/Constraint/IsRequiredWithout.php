<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsRequiredWithout extends Constraint
{
    public $otherField;
    public $message = 'This field is required when {{otherField}} is not present.';
}
