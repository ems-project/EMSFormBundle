<?php

declare(strict_types=1);

namespace EMS\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FieldExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['ems_config_id'] = $options['ems_config_id'];
        $view->vars['ems_translation_domain'] = $options['ems_translation_domain'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'ems_config_id' => null,
            'ems_translation_domain' => null,
        ]);
    }

    public function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
