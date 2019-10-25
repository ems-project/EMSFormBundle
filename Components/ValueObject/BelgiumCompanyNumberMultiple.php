<?php

namespace EMS\FormBundle\Components\ValueObject;

class BelgiumCompanyNumberMultiple
{
    /** @var NumberValue */
    private $number;

    public function __construct(string $companyNumbers)
    {
        $this->number = new NumberValue($companyNumbers);

        if (!$this->validate()) {
            throw new \Exception(sprintf('At least one company registration number data: %s', $companyNumbers));
        }
    }

    public function validate(): bool
    {
        if (strlen($this->number->getDigits()) % 10) {
            return false;
        }
        
        $numbers = str_split($this->number->getDigits(), 10);
        foreach ($numbers as $number) {
            try {
                new BelgiumCompanyNumber($number);
            } catch (\Exception $exception) {
                return false;
            }
        }
        
        return true;
    } 
    
    public function getValidatedInput(): string
    {
        $numbers = str_split($this->number->getDigits(), 10);
        $data = '';
        foreach ($numbers as $key => $number) {
            $data .= ($key == 0) ? $number : ' ' . $number;                   
        }
        return $data;
    }
}
