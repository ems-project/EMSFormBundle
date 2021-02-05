<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsExpression extends Constraint
{
    public $expression;
    public $message = 'This value is not valid.';

    public function getRequiredOptions()
    {
        return ['expression'];
    }
}
