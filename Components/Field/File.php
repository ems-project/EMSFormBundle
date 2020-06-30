<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Form\FileType;

class File extends AbstractField
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
        $options['ems_translation_domain'] = $this->config->getParentForm()->getTranslationDomain();

        return $options;
    }
}
