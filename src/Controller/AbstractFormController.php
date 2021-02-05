<?php

namespace EMS\FormBundle\Controller;

use EMS\FormBundle\FormConfig\FormConfig;
use Symfony\Component\Form\FormInterface;

abstract class AbstractFormController
{
    protected function getFormConfig(FormInterface $form): FormConfig
    {
        $config = $form->getConfig()->getOption('config');

        if (!$config instanceof FormConfig) {
            throw new \RuntimeException('invalid form config');
        }

        return $config;
    }

    protected function getFormOptions(string $ouuid, string $locale): array
    {
        return ['ouuid' => $ouuid, 'locale' => $locale];
    }

    protected function getDisabledValidationsFormOptions(string $ouuid, string $locale): array
    {
        return \array_merge($this->getFormOptions($ouuid, $locale), ['validation_groups' => false]);
    }
}
