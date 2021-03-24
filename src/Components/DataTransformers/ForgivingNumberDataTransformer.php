<?php

namespace EMS\FormBundle\Components\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;

class ForgivingNumberDataTransformer implements DataTransformerInterface
{
    /** @var string[] */
    private array $transformerClasses;

    /**
     * @param string[] $transformerClasses
     */
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
        if (null === $value) {
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

        return $value;
    }
}
