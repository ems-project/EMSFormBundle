<?php

namespace EMS\FormBundle\Components\Form;

use EMS\FormBundle\FormConfig\MarkupConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarkupType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['config'] = $options['config'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('config')
            ->setAllowedTypes('config', MarkupConfig::class)
        ;
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function getBlockPrefix()
    {
        return 'ems_markup';
    }
}
