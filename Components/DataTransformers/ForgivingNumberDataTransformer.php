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
        if ($value != null) {
            if (count($this->valuesObject) == 0) {
                $number = new NumberValue($value);
                return $number->getDigits();
            }
            foreach ($this->valuesObject as $class) {
                try {
                    $validation = new $class($value);
                    return $validation->getValidatedInput();
                } catch (\Exception $exception) {
                    continue;
                }
            }
            throw new TransformationFailedException(sprintf(
                'Is not a valid number "%s"',
                $value
            ));
        }
    }
}
