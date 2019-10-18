<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceSelectMultiple extends AbstractField
{
    public function getFieldClass(): string
    {
        return ChoiceType::class;
    }

    public function getId(): string
    {
        return 'select_multiple';
    }

    public function getOptions(): array
    {
        $options = parent::getOptions();
        $options['choices'] = $this->config->getChoiceList();
        $options['data'] = [$this->config->getDefaultValue()];
        $options['expanded'] = false;
        $options['multiple'] = true;

        return $options;
    }
}
