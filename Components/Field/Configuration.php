<?php

namespace EMS\FormBundle\Components\Field;

use EMS\FormBundle\Components\Validation\Configuration as ValidationConfiguration;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Configuration
{
    /** @var string */
    private $label;

    /** @var ?string */
    private $help;

    /** @var string */
    private $type;

    /** @var ValidationConfiguration[] */
    private $validations = [];

    /** @var string[] */
    private $failures = [];

    public function __construct(array $fieldDefinition, string $locale)
    {
        if (!array_key_exists('type', $fieldDefinition)) {
            throw new \Exception('Field type should be defined to create a field');
        }

        $this->label = $fieldDefinition["label_$locale"] ?? 'label';
        $this->help = $fieldDefinition["help_$locale"] ?? null;
        $this->type = $fieldDefinition['type']['_id'];

        array_map([$this, 'addValidation'], $fieldDefinition['type']['_source']['validations'] ?? []);
        array_map([$this, 'addValidation'], $fieldDefinition['validations'] ?? []);
    }

    public function build(FormBuilderInterface $builder)
    {
        $builder
            ->add($this->label, $this->getFieldClass(), [
                'required' => $this->isRequired(),
                'label' => $this->label,
                'help' => $this->getHelp(),
                'attr' => $this->getAttributes(),
                'constraints' => $this->getValidationConstraints()
            ]);
    }

    public function getFailures(): array
    {
        return $this->failures;
    }

    private function getHelp(): ?string
    {
        return $this->help;
    }

    private function getAttributes(): array
    {
        return array_merge($this->getHtml5ValidationAttributes(), ['class' => $this->getId()]);
    }

    private function getHtml5ValidationAttributes(): array
    {
        return array_reduce(
            $this->validations,
            function ($acc, $validation) {
                /** @var ValidationConfiguration $validation */
                return array_merge($validation->getHtml5Attribute(), $acc);
            },
            []
        );
    }

    private function isRequired(): bool
    {
        return array_key_exists('required', $this->validations);
    }

    private function getValidationConstraints(): array
    {
        return array_reduce(
            $this->validations,
            function ($acc, $validation) {
                /** @var ValidationConfiguration $validation */
                $acc[] = $validation->build();
                return $acc;
            },
            []
        );
    }

    private function addValidation(array $validation)
    {
        if ($validation === []) {
            return;
        }

        try {
            $className = preg_replace('/\s/', '', ucwords(strtolower($validation['validation']['_source']['name'])));
            $class = sprintf('App\Components\Validation\%s', $className);
            /** @var ValidationConfiguration $validation */
            $validation = new $class($validation);
            $this->validations[$validation->getId()] = $validation;
        } catch (\Throwable $exception) {
            $this->failures[] = sprintf('Validation not implemented: %s', $exception->getMessage());
        }
    }

    abstract public function getId(): string;

    abstract protected function getFieldClass(): string;
}
