<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;

class Required extends Configuration
{
    public function getId(): string
    {
        return 'required';
    }

    public function build(): Constraint
    {
        return new NotBlank();
    }

    public function getHtml5Attribute(): array
    {
        return []; //Symfony Forms handles this case.
    }
}
