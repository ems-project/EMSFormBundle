<?php

namespace EMS\FormBundle\Form;

use EMS\FormBundle\FormConfig\FormConfig;
use Symfony\Component\Form\FormInterface;

class Form
{
    /** @var FormInterface */
    public $form;
    /** @var FormConfig */
    public $config;

    public function __construct(FormInterface $form, FormConfig $config)
    {
        $this->form = $form;
        $this->config = $config;
    }
}
