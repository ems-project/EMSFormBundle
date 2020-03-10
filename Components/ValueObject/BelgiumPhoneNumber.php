<?php

namespace EMS\FormBundle\Components\ValueObject;

class BelgiumPhoneNumber
{
    /** @var NumberValue */
    private $number;

    const LOCAL = 'local';
    const INTERNATIONAL_PLUS = 'plus';
    const INTERNATIONAL_ZEROS = 'zeros';

    public function __construct(string $phone)
    {
        $this->number = new NumberValue($phone);

        if (!$this->validate()) {
            throw new \Exception(sprintf('invalid phone data: %s', $phone));
        }
    }

    public function validate(): bool
    {
        $numberType = $this->getNumberType();

        if ($this->validateNumberOfDigit($numberType) && $this->validateCountryCode($numberType) && $this->validateLongDistanceCode($numberType)) {
            return true;
        }

        return false;
    }

    private function validateNumberOfDigit(string $numberType): bool
    {
        $numberOfDigits = strlen($this->number->getDigits());

        if ($numberType === self::INTERNATIONAL_ZEROS) {
            return ($numberOfDigits === 13) || ($numberOfDigits === 12);
        }

        if ($numberType === self::INTERNATIONAL_PLUS) {
            return ($numberOfDigits === 11) || ($numberOfDigits === 10);
        }

        if ($numberType === self::LOCAL) {
            return ($numberOfDigits === 10) || ($numberOfDigits === 9);
        }

        return false;
    }

    private function validateCountryCode(string $numberType)
    {
        if ($numberType === self::INTERNATIONAL_ZEROS) {
            return strpos($this->number->getInput(), '32') === 2;
        }

        if ($numberType === self::INTERNATIONAL_PLUS) {
            return strpos($this->number->getInput(), '32') === 1;
        }

        if ($numberType === self::LOCAL) {
            return true;
        }

        return false;
    }

    private function validateLongDistanceCode(string $numberType)
    {
        if ($numberType === self::INTERNATIONAL_ZEROS) {
            return strpos($this->number->getInput(), '0', 2) !== 4;
        }

        if ($numberType === self::INTERNATIONAL_PLUS) {
            return strpos($this->number->getInput(), '0') !== 3;
        }

        if ($numberType === self::LOCAL) {
            return strpos($this->number->getInput(), '0') === 0;
        }

        return false;
    }

    private function getNumberType(): string
    {
        if (strpos($this->number->getInput(), '+') === 0) {
            return self::INTERNATIONAL_PLUS;
        }

        if (strpos($this->number->getInput(), '00') === 0) {
            return self::INTERNATIONAL_ZEROS;
        }

        return self::LOCAL;
    }
    
    public function transform(): string
    {
        if (strpos($this->number->getInput(), '+') === 0) {
            return '+' .  $this->number->getDigits();
        }

        return $this->number->getDigits();
    }
}
