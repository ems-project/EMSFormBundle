<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\TextType;

final class Text extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'text';
    }

    public function getFieldClass(): string
    {
        return TextType::class;
    }
}
