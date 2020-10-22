<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

final class DateWithPicker extends Date
{
    public function getHtmlClass(): string
    {
        return 'date-with-picker';
    }
}
