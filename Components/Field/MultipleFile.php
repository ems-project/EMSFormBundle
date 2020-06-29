<?php

namespace EMS\FormBundle\Components\Field;

class MultipleFile extends File
{
    public function getOptions(): array
    {
        $options = parent::getOptions();
        $options['multiple'] = true;

        return $options;
    }
}
