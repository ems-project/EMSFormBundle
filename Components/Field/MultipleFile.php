<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class MultipleFile extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'file';
    }

    public function getFieldClass(): string
    {
        return FileType::class;
    }

    public function getOptions(): array
    {
        $options = parent::getOptions();
        $options['multiple'] = true;

        return $options;
    }
}
