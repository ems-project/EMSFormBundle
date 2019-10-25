<?php

namespace EMS\FormBundle\Components\DataTransformers;

use EMS\FormBundle\Components\ValueObject\NumberValue;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ForgivingNumberDataTransformer implements DataTransformerInterface
{
    /** @var array */
    private $valuesObject;
    
    public function __construct(array $valuesObject)
    {
        $this->valuesObject = $valuesObject;
    }
    
    public function transform($value)
    { 
        return $value;
    }

    public function reverseTransform($value)
    {
        if (count($this->valuesObject) == 0 ) {
            $number = new NumberValue($value);
            return $number->getDigits();
        }
        foreach ($this->valuesObject as $class) {
            $number = new $class($value);
            if ($number->validate()) {
                return $number->getValidatedInput();
            }
        }
        throw new TransformationFailedException(sprintf(
            'Is not a valid number "%s"',
            $value
        ));
    }
}
