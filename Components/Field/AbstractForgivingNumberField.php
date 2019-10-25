<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\DataTransformers\ForgivingNumberDataTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractForgivingNumberField extends AbstractField
{
    public function getFieldClass(): string
    {
        return TextType::class;
    }
    
    public function getValueObjects() : array 
    {
        return [];
    }
    
    public function getDataTransformer() : ? DataTransformerInterface
    {
        return new ForgivingNumberDataTransformer( $this->getValueObjects() );
    }
}
