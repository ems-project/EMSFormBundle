<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Form\VerificationCodeType;
use EMS\FormBundle\Components\Validation\VerificationCode;

class SmsConfirmation extends AbstractField
{
    public function getHtmlClass(): string
    {
        return 'number';
    }

    public function getFieldClass(): string
    {
        return VerificationCodeType::class;
    }

    public function getOptions(): array
    {
        $options = parent::getOptions();
        $options['block_prefix'] = 'ems_sms_confirmation';
        $options['token_id'] = $this->config->getId();

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
