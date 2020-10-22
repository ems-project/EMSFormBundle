<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class Checkbox extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'checkbox';
    }

    public function getFieldClass(): string
    {
        return CheckboxType::class;
    }
}
