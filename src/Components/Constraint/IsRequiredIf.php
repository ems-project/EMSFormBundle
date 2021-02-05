<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsRequiredIf extends Constraint
{
    public ?string $expression;
    public string $message = 'This value should not be blank.';

    public function getRequiredOptions()
    {
        return ['expression'];
    }
}
