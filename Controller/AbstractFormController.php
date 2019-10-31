<?php

namespace EMS\FormBundle\Controller;

abstract class AbstractFormController
{
    protected function getFormOptions(string $ouuid, string $locale, bool $validation = true)
    {
        $formOptions = [
            'ouuid' => $ouuid,
            'locale' => $locale,
        ];

        if ($validation === false) {
            $formOptions['validation_groups'] = false;
        }

        return $formOptions;
    }
}
