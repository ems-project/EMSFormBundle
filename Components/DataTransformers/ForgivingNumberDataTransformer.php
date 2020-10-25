<?php

namespace EMS\FormBundle\Components\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ForgivingNumberDataTransformer implements DataTransformerInterface
{
    /** @var array */
    private $transformerClasses;

    public function __construct(array $transformerClasses)
    {
        $this->transformerClasses = $transformerClasses;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return;
        }

        foreach ($this->transformerClasses as $class) {
            try {
                $validation = new $class($value);
                return $validation->transform();
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
