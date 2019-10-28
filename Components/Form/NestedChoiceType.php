<?php

namespace EMS\FormBundle\Components\Form;

use EMS\FormBundle\Components\Field\ChoiceSelect;
use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\Components\Form;
use EMS\FormBundle\FormConfig\ElementInterface;
use EMS\FormBundle\FormConfig\FieldChoicesConfig;
use EMS\FormBundle\FormConfig\FieldConfig;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NestedChoiceType extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FieldConfig $config */
        $config = $this->getFieldConfig($options);
        $config->setClassName(ChoiceSelect::class);
        $choices = $config->getChoices();
        $field = $this->createField($config);

        $builder->add('level_0', $field->getFieldClass(), $field->getOptions());

        for ($i = 1; $i <= $choices->getMaxLevel(); $i++) {
            $fieldName = sprintf('level_%d', $i);
            $builder->add($fieldName, HiddenType::class);
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($choices, $field, $i) {
                $this->updateNestedFields(
                    $event->getForm(),
                    $event->getForm()->getData(),
                    $choices,
                    $field
                );
            }
        );
    }

    private function updateNestedFields(FormInterface $form, array $data, FieldChoicesConfig $choices, FieldInterface $field): void
    {
        foreach ($data as $name => $choice) {
            if ($choice === null) {
                continue;
            }

            $nestedName = $this->nextFieldName($name);
            if (!$form->has($nestedName)) {
                return;
            }

            $choices->addChoice($choice);
            $form->remove($name);
            $form->add($name, $field->getFieldClass(), \array_merge($field->getOptions(), ['choices' => $choices->list()]));

        }
    }

    private function nextFieldName(string $name): string
    {
        $split = \explode('_', $name);
        return \sprintf('level_%d',  ($split[1] + 1));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['field-config'] = $options['field-config'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('field-config')
            ->setAllowedTypes('field-config', FieldConfig::class)
        ;
    }

    private function getFieldConfig(array $options): FieldConfig
    {
        if (isset($options['field-config'])) {
            return $options['field-config'];
        }

        throw new \Exception('Could not build form, nested choice field config missing!');
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function getBlockPrefix()
    {
        return 'ems_nested_choice';
    }
}
