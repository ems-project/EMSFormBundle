<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\FormBundle\FormConfig\ElementInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FormDataFile
{
    /** @var UploadedFile */
    private $file;
    /** @var ElementInterface */
    private $formElement;

    public function __construct(UploadedFile $file, ElementInterface $formElement)
    {
        $this->file = $file;
        $this->formElement = $formElement;
    }

    public function base64(): ?string
    {
        $content = file_get_contents($this->file->getPathname());

        return $content ? base64_encode($content) : null;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function toArray(): array
    {
        return [
            'filename' => $this->file->getClientOriginalName(),
            'pathname' => $this->file->getPathname(),
            'mimeType' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'form_field' => $this->formElement->getName()
        ];
    }
}
