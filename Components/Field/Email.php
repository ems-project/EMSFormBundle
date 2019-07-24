<?php

namespace EMS\FormBundle\Components\Field;

use Symfony\Component\Form\Extension\Core\Type\EmailType;

class Email extends AbstractField
{
    public function getFieldClass(): string
    {
        return EmailType::class;
    }

    public function getId(): string
    {
        return 'email';
    }
}
