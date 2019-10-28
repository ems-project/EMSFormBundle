<?php

namespace EMS\FormBundle\Components;

use EMS\FormBundle\Components\Field\ChoiceSelectNested;
use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\FormConfig\AbstractFormConfig;
use EMS\FormBundle\FormConfig\FieldChoicesConfig;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use EMS\FormBundle\FormConfig\MarkupConfig;
use EMS\FormBundle\FormConfig\SubFormConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /** @var FormConfigFactory */
    private $configFactory;

    public function __construct(FormConfigFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->getConfig($options);

        foreach ($config->getElements() as $element) {
            if ($element instanceof FieldConfig) {
                $this->addField($builder, $element);
            } elseif ($element instanceof MarkupConfig || $element instanceof SubFormConfig) {
                $builder->add($element->getName(), $element->getClassName(), ['config' => $element]);
            }

        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['form_config'] = $options['config'];

        parent::buildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['ouuid', 'locale'])
            ->setDefault('config', null)
            ->setNormalizer('config', function (Options $options, $value) {
                return $value ? $value : $this->configFactory->create($options['ouuid'], $options['locale']);
            })
            ->setNormalizer('attr', function (Options $options, $value) {
                if (!isset($options['config'])) {
                    return $value;
                }

                /** @var FormConfig $config */
                $config = $options['config'];
                $value['id'] = $config->getId();
                $value['class'] = $config->getLocale();

                return $value;
            })
        ;
    }

    private function getConfig(array $options): AbstractFormConfig
    {
        if (isset($options['config'])) {
            return $options['config'];
        }

        throw new \Exception('Could not build form, config missing!');
    }

    protected function createField(FieldConfig $config): FieldInterface
    {
        $class = $config->getClassName();

        return new $class($config);
    }

    private function addField(FormBuilderInterface $builder, FieldConfig $element): void
    {
        $field = $this->createField($element);
        $configOption = ['field-config' => $element];
        $options = $element->getClassName() !== ChoiceSelectNested::class ? $field->getOptions() : \array_merge($field->getOptions(), $configOption);

        $builder->add($element->getName(), $field->getFieldClass(), $options);
    }

    private function addChoiceSelectSubLevel(FormBuilderInterface $builder, FormInterface $form, FieldConfig $config, FieldChoicesConfig $choices, ?string $data, int $level = 1): void
    {
        if (is_string($data)) {
            $choices->addChoice($data);
        }

        if (!$choices->hasNextLevel()) {
            return;
        }

        if ($choices->getMaxLevel() < $level) {
            return;
        }

        $fieldName = sprintf('%s-level-%d', $config->getName(), $level);

        if ($choices->hasChoosen()) {
            $field = $this->createField($config);
            $form->add(
                $fieldName,
                $field->getFieldClass(),
                $field->getOptions()
            );
            //$this->addChoiceSelectEventListener($builder, $config, $choices, $fieldName);
            return;
        }

        $form->add($fieldName, HiddenType::class);
        //$this->addChoiceSelectSubLevel($builder, $form, $config, $choices, null, $level + 1);
        //$this->addChoiceSelectEventListener($builder, $config, $choices, $fieldName);
    }
}
