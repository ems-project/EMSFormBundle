<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BelgiumOnssRszNumber;

final class OnssRsz extends AbstractForgivingNumberField
{
    public function getHtmlClass(): string
    {
        return 'onss-rsz';
    }

    public function getTransformerClasses(): array
    {
        return [BelgiumOnssRszNumber::class];
    }
}
