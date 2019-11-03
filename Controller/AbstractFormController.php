<?php

namespace EMS\FormBundle\Controller;

abstract class AbstractFormController
{
    protected function getFormOptions(string $ouuid, string $locale): array
    {
        return ['ouuid' => $ouuid, 'locale' => $locale];
    }

    protected function getDisabledValidationsFormOptions(string $ouuid, string $locale): array
    {
        return \array_merge($this->getFormOptions($ouuid, $locale), ['validation_groups' => false]);
    }
}
