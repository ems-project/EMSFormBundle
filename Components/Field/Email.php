<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\EmailType;

class Email extends Configuration
{
    protected function getFieldClass(): string
    {
        return EmailType::class;
    }

    public function getId(): string
    {
        return 'email';
    }
}
