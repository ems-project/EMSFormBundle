<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Email as EmailValidation;

class Email extends Configuration
{
    public function getId(): string
    {
        return 'email';
    }

    public function build(): Constraint
    {
        return new EmailValidation(['mode' => EmailValidation::VALIDATION_MODE_HTML5]);
    }

    public function getHtml5Attribute(): array
    {
        return []; //Symfony framework.validation config handles this case.
    }
}
