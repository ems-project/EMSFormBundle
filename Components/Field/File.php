<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class File extends AbstractField
{
    public function getFieldClass(): string
    {
        return FileType::class;
    }

    public function getId(): string
    {
        return 'file';
    }
}
