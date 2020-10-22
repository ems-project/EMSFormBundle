<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;

final class FileMimeTypes extends AbstractValidation
{
    public function getHtml5AttributeName(): string
    {
        return 'accept';
    }

    public function getConstraint(): Constraint
    {
        return new File(['mimeTypes' => \explode(',', $this->value)]);
    }
}
