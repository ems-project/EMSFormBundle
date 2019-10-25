<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BelgiumOnssRszNumber;

class OnssRsz extends AbstractForgivingNumberField
{
    public function getId(): string
    {
        return 'onss-rsz';
    }
    
    public function getValueObjects() : array
    {
        return [BelgiumOnssRszNumber::class];
    }
}
