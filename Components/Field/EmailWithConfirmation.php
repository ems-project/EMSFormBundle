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
        $label = $this->config->getLabel();
        $confirmLabel = lcfirst($label);

        $options = parent::getOptions();
        $options['type'] = EmailType::class;
        $options['first_options'] = ['label' => $label];
        $options['second_options'] = [
            'label' => 'Confirm %field%',
            'label_translation_parameters' => ['%field%' => $confirmLabel],
            'translation_domain' => 'validators',
            ];
        $options['invalid_message'] = 'The "{{field1}}" and "Confirm {{field2}}" fields must match.';
        $options['invalid_message_parameters'] = [
            '{{field1}}' => $label,
            '{{field2}}' => $confirmLabel,
        ];

        return $options;
    }
}
