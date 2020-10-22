<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class IsRequiredIf extends Constraint
{
    public $expression;
    public $message = 'This value should not be blank.';

    public function getRequiredOptions()
    {
        return ['expression'];
    }
}
