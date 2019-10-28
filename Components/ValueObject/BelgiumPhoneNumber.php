<?php

namespace EMS\FormBundle\Components\ValueObject;

use phpDocumentor\Reflection\Types\This;

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
        $numberOfDigits = strlen($this->number->getDigits());

        if ($this->getInternationalType() === self::INTERNATIONAL_ZEROS) {
            return ($numberOfDigits === 13) || ($numberOfDigits === 12);
        }

        if ($this->getInternationalType() === self::INTERNATIONAL_PLUS) {
            return ($numberOfDigits === 11) || ($numberOfDigits === 10);
        }

        if ($this->getInternationalType() === self::LOCAL) {
            return ($numberOfDigits === 10) || ($numberOfDigits === 9);
        }

        return false;
    }

    private function getInternationalType(): string
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
