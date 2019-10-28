<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BisNumber;
use EMS\FormBundle\Components\ValueObject\RrNumber;

class NissInsz extends AbstractForgivingNumberField
{
    public function getId(): string
    {
        return 'niss-insz';
    }
    
    public function getValueObjects(): array
    {
        return [BisNumber::class, RrNumber::class];
    }
}
