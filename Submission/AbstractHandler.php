<?php

namespace EMS\FormBundle\Submission;

use EMS\FormBundle\Submit\AbstractResponse;

abstract class AbstractHandler
{
    public function canHandle(string $class): bool
    {
        return $class === get_called_class();
    }

    abstract public function handle(HandleRequestInterface $handleRequest): AbstractResponse;
}
