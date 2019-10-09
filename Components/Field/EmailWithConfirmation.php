<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EmailWithConfirmation extends AbstractField
{

    public function getId(): string
    {
        return 'email_with_confirmation';
    }

    public function getFieldClass(): string
    {
        return RepeatedType::class;
    }

    public function getOptions(): array
    {
        $options = parent::getOptions();
        $options['type'] = EmailType::class;
        $options['first_options'] = ['label' => $this->config->getLabel()];
        $options['second_options'] = ['label' => "repeat".$this->config->getLabel()];
        
        return $options;
    }
}
