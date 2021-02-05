<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

use EMS\FormBundle\Components\Field\File;
use EMS\FormBundle\Components\Field\MultipleFile;
use EMS\FormBundle\FormConfig\FormConfig;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FormData
{
    private FormConfig $formConfig;
    /** @var array<int|string, mixed> */
    private array $raw;

    /**
     * @param FormInterface<FormInterface> $form
     */
    public function __construct(FormConfig $formConfig, FormInterface $form)
    {
        $this->formConfig = $formConfig;

        $formData = $form->getData();
        $this->raw = \is_array($formData) ? $formData : [];
    }

    /** @return array<int|string, mixed> */
    public function raw(): array
    {
        return $this->raw;
    }

    /** @return FormDataFile[] */
    public function getAllFiles(): array
    {
        $files = [];

        foreach ($this->raw as $formField => $value) {
            $element = $this->formConfig->getElementByName($formField);

            if (null === $element || !\in_array($element->getClassName(), [MultipleFile::class, File::class])) {
                continue;
            }

            $uploadedFiles = \is_array($value) ? $value : [$value];

            foreach ($uploadedFiles as $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {
                    $files[] = new FormDataFile($uploadedFile, $element);
                }
            }
        }

        return $files;
    }
}
