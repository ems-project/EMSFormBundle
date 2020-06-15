<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

interface HandleResponseInterface
{
    public function getStatus(): string;
    public function getResponse(): string;
}
