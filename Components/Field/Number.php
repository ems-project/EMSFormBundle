<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class Number extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'number';
    }

    public function getFieldClass(): string
    {
        return NumberType::class;
    }
}
