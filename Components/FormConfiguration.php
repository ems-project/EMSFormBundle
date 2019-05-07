<?php

namespace EMS\FormBundle\Components;

use EMS\FormBundle\Components\Field\Configuration as FieldConfiguration;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormConfiguration
{
    /** @var string */
    private $id;

    /** @var FieldConfiguration[] $fields */
    private $fields = [];

    /** @var string */
    private $locale;

    /** @var string */
    private $formTheme;

    /** @var string[] */
    private $domains;

    /** @var string[] $failures */
    private $failures = [];

    public function __construct(array $formDefinition, string $formThemeField, string $id, string $locale, array $domains)
    {
        $this->id = $id;
        $this->locale = $locale;
        $this->formTheme = $formDefinition[$formThemeField];
        $this->domains = $domains;
        array_map([$this, 'addField'], $formDefinition['form']['fields'] ?? []);
    }

    public function getFormTheme(): string
    {
        return $this->formTheme;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getForm(FormFactoryInterface $formFactory): FormInterface
    {
        $builder = $formFactory->createNamedBuilder(
            sprintf('%s-form', $this->id),
            FormType::class,
            null,
            ['attr' => ['id' => $this->id, 'class' => $this->locale]]
        );

        array_map(
            function ($field) use ($builder) {
                /** @var FieldConfiguration $field */
                $field->build($builder);
            },
            $this->fields
        );

        //TODO do not hard code submit field name (text)
        return $builder->add('submit', SubmitType::class)->getForm();
    }

    private function addField(array $field)
    {
        if ($field === []) {
            return;
        }

        try {
            $className = preg_replace('/\s/', '', ucwords(strtolower($field['type']['_source']['name'])));
            $class = sprintf('%s\Field\%s', __NAMESPACE__, $className);
            /** @var FieldConfiguration $field */
            $field = new $class($field, $this->locale);
            $this->fields[] = $field;
            $this->failures = array_merge($this->failures, $field->getFailures());
        } catch (\Throwable $exception) {
            $this->failures[] = sprintf('Field not implemented: %s', $exception->getMessage());
        }
    }
}
