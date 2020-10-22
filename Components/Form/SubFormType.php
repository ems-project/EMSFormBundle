<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Form;

use EMS\FormBundle\Components\Form;
use EMS\FormBundle\FormConfig\SubFormConfig;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SubFormType extends Form
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['config'] = $options['config'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('config')
            ->setAllowedTypes('config', SubFormConfig::class)
        ;
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function getBlockPrefix()
    {
        return 'ems_subform';
    }
}
