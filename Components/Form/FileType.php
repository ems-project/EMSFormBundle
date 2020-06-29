<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileType extends SymfonyFileType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setRequired('ems_translation_domain');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['ems_translation_domain'] = $options['ems_translation_domain'];
    }

    public function getBlockPrefix(): string
    {
        return 'ems_file';
    }
}
