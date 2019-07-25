<?php

namespace EMS\FormBundle\Components\Validation;

use Symfony\Component\Validator\Constraint;

interface ValidationInterface
{
    public function getId(): string;

    public function getConstraint(): Constraint;

    public function getHtml5Attribute(): array;
}
