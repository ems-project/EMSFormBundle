<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class Textarea extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'textarea';
    }

    public function getFieldClass(): string
    {
        return TextareaType::class;
    }
}
