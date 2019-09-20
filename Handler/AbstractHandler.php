<?php

namespace EMS\FormBundle\Handler;

use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\SubmissionConfig;
use EMS\FormBundle\Submit\ResponseCollector;
use EMS\FormBundle\Submit\AbstractResponse;
use Symfony\Component\Form\FormInterface;

abstract class AbstractHandler
{
    public function canHandle(string $class): bool
    {
        return $class === get_called_class();
    }

    abstract public function handle(SubmissionConfig $submission, FormInterface $form, FormConfig $config, AbstractResponse $respones = null): AbstractResponse;
}
