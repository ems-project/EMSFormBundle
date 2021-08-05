<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use Symfony\Component\Validator\Constraint;

final class IsBirthDate extends Constraint
{
    public string $age = 'now';
    public string $message = 'The date "{{date}}" must be in the past.';
    public string $messageAge = 'The date "{{date}}" must be at least before "{{age}}".';

    /** @return string[] */
    public function getRequiredOptions(): array
    {
        return ['age'];
    }
}
