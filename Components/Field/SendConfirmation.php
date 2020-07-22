<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Form\SendConfirmationType;
use EMS\FormBundle\Components\Validation\VerificationCode;

class SendConfirmation extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'number';
    }

    public function getFieldClass(): string
    {
        return SendConfirmationType::class;
    }

    public function getOptions(): array
    {
        $options = parent::getOptions();
        $options['token_id'] = $this->config->getId();
        $options['ems_translation_domain'] = $this->config->getParentForm()->getTranslationDomain();

        $validation = $this->getVerificationCodeValidation();
        if ($validation) {
            $options['value_field'] = $validation->getField();
        }

        return $options;
    }

    private function getVerificationCodeValidation(): ?VerificationCode
    {
        foreach ($this->getValidations() as $validation) {
            if ($validation instanceof VerificationCode) {
                return $validation;
            }
        }

        return null;
    }
}
