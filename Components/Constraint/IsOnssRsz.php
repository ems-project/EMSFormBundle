<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class IsOnssRsz extends Constraint
{
    public $message = 'The NSSO number "{{string}}" is invalid.';
}
