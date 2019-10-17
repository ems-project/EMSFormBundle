<?php

namespace EMS\FormBundle\Components\ValueObject;

class BelgiumCompanyNumber
{
    /** @var NumberValue */
    private $number;

    public function __construct(string $phone)
    {
        $this->number = new NumberValue($phone);

        if (!$this->validate()) {
            throw new \Exception(sprintf('invalid company registration number data: %s', $phone));
        }
    }

    public function validate(): bool
    {
        $numberOfDigits = strlen($this->number->getDigits());
        $firstDigit = substr($this->number->getDigits(), 0, 1);

        return (($numberOfDigits === 10) && ($firstDigit === '0'));
    }
}
