<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Submit extends AbstractField
{
    public function getFieldClass(): string
    {
        return SubmitType::class;
    }

    public function getId(): string
    {
        return 'submit';
    }

    public function getOptions(): array
    {
        return [
            'attr' => array_merge($this->getAttributes(), ['class' => 'btn-primary']),
            'label' => $this->config->getLabel(),
        ];
    }
}
