<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VerificationCodeType extends TextType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(['value_field', 'token_id']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['value_field'] = $options['value_field'];
        $view->vars['token_id'] = $options['token_id'];
    }

    public function getBlockPrefix(): string
    {
        return 'ems_verification_code';
    }
}
