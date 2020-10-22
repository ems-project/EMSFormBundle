<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class IsNissInsz extends Constraint
{
    public $message = 'The social security number "{{string}}" is invalid.';
}
