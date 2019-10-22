<?php 

namespace EMS\FormBundle\Components\ValueObject;

class BelgiumOnssRszNumber
{
    /** @var NumberValue */
    private $number;


    public function __construct(string $phone)
    {
        $this->number = new NumberValue($phone);

        if (!$this->validate()) {
            throw new \Exception(sprintf('invalid national social security office data: %s', $phone));
        }
    }

    public function validate(): bool
    {
        $numberOfDigits = strlen($this->number->getDigits());
        
        return (($numberOfDigits >= 9) and ($numberOfDigits <= 10));
    }
 
}
