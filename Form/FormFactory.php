<?php

namespace EMS\FormBundle\Form;

use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory as SymfonyFormFactory;

class FormFactory
{
    /** @var FormConfigFactory */
    private $formConfigFactory;
    /** @var SymfonyFormFactory */
    private $formFactory;

    public function __construct(FormConfigFactory $formConfigFactory, SymfonyFormFactory $formFactory)
    {
        $this->formConfigFactory = $formConfigFactory;
        $this->formFactory = $formFactory;
    }

    public function create(string $ouuid, string $locale): Form
    {
        $config = $this->formConfigFactory->create($ouuid, $locale);
        $builder = $this->createBuilder($config);

        foreach ($config->getFields() as $fieldConfig) {
            $field = $this->createField($fieldConfig);
            $builder->add($fieldConfig->getName(), $field->getFieldClass(), $field->getOptions());
        }

        //@todo submit/buttons should be dynamic
        $builder->add('submit', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);

        return new Form($builder->getForm(), $config);
    }

    private function createField(FieldConfig $config): FieldInterface
    {
        $class = $config->getClass();

        return new $class($config);
    }

    private function createBuilder(FormConfig $config): FormBuilderInterface
    {
         return $this->formFactory->createNamedBuilder(
             sprintf('%s-form', $config->getName()),
             FormType::class,
             null,
             ['attr' => ['id' => $config->getName(), 'class' => $config->getLocale()]]
         );
    }
}
