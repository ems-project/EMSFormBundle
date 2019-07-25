<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Radio extends AbstractField
{
    public function getFieldClass(): string
    {
        return ChoiceType::class;
    }

    public function getId(): string
    {
        return 'text';
    }

    public function getOptions(): array
    {
        $options = parent::getOptions();

        $options['expanded'] = true;
        $options['multiple'] = false;
        $options['choices'] = $this->config->getChoices()->list();

        return $options;
    }
}
