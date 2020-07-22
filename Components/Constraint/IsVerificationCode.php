<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

final class IsVerificationCode extends Constraint
{
    /** @var string */
    public $field;
    /** @var string */
    public $message = 'The verification code "{{code}}" is not valid.';

    public function getRequiredOptions()
    {
        return ['field'];
    }
}
