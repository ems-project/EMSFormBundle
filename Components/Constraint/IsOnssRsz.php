<?php

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsOnssRsz extends Constraint
{
    public $message = 'The national social security office number "{{string}}" has an invalid format.';
}
