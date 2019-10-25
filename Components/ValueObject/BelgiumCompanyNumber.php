<?php

namespace EMS\FormBundle\Components\ValueObject;

class BelgiumCompanyNumber
{
    /** @var NumberValue */
    private $number;

    public function __construct(string $companyNumber)
    {
        $this->number = new NumberValue($companyNumber);

        if (!$this->validate()) {
            throw new \Exception(sprintf('invalid company registration number data: %s', $companyNumber));
        }
    }

    public function validate(): bool
    {
        $numberOfDigits = strlen($this->number->getDigits());
        $firstDigit = substr($this->number->getDigits(), 0, 1);

        return (($numberOfDigits === 10) && (($firstDigit === '0') || ($firstDigit === '1')));
    }
    
    public function getValidatedInput(): string
    {
        return $this->number->getDigits();
    }
}
