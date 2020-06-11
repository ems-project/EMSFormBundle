<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\FormBundle\FormConfig\FormConfig;
use Symfony\Component\Form\FormInterface;

interface HandleRequestInterface
{
    public function addResponse(HandleResponseInterface $response): void;

    public function getClass(): string;
    public function getForm(): FormInterface;
    public function getFormData(): array;
    public function getFormConfig(): FormConfig;
    public function getEndPoint(): string;
    public function getMessage(): string;

    /**
     * @return HandleResponseInterface[]
     */
    public function getResponses(): array;
}
