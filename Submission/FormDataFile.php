<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\CommonBundle\Twig\RequestRuntime;
use EMS\FormBundle\FormConfig\ElementInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

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
        $fileName = $this->getFilename($this->file, $this->formElement->getName());
        return [
            'filename' => $fileName,
            'pathname' => $this->file->getPathname(),
            'mimeType' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'form_field' => $this->formElement->getName()
        ];
    }

    private function getFilename(UploadedFile $uploadedFile, string $fieldName)
    {
        $filename = $uploadedFile->getClientOriginalName();
        $extension = MimeTypes::getDefault()->getExtensions($uploadedFile->getClientMimeType())[0] ?? null;
        if ($extension !== null && !RequestRuntime::endsWith($filename, $extension)) {
            $filename .= \sprintf('.%s', $extension);
        }
        return sprintf('%s.%s', \uniqid(sprintf('%s.', $fieldName), false), $filename);
    }
}
