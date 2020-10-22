<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\ValueObject\BelgiumCompanyNumber;

final class CompanyNumber extends AbstractForgivingNumberField
{
    public function getHtmlClass(): string
    {
        return 'company-number';
    }

    public function getTransformerClasses(): array
    {
        return [BelgiumCompanyNumber::class];
    }
}
