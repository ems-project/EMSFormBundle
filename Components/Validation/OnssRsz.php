<?php

namespace EMS\FormBundle\Components\Validation;

use EMS\FormBundle\Components\Constraint\IsOnssRsz;
use Symfony\Component\Validator\Constraint;
use EMS\FormBundle\Components\ValueObject\BelgiumOnssRszNumber;

class OnssRsz extends AbstractValidation
{
    public function getId(): string
    {
        return 'onss-rsz';
    }

    public function getConstraint(): Constraint
    {
        return new IsOnssRsz($this->value);
    }

    public function getHtml5Attribute(): array
    {
        return [];
    }
}
