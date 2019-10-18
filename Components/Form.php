<?php

namespace EMS\FormBundle\Components;

use EMS\FormBundle\Components\Field\ChoiceSelect;
use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\FormConfig\AbstractFormConfig;
use EMS\FormBundle\FormConfig\ElementInterface;
use EMS\FormBundle\FormConfig\FieldChoicesConfig;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use EMS\FormBundle\FormConfig\MarkupConfig;
use EMS\FormBundle\FormConfig\SubFormConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                $field = $this->createField($element);
                $builder->add($element->getName(), $field->getFieldClass(), $field->getOptions());
            } elseif ($element instanceof MarkupConfig || $element instanceof SubFormConfig) {
                $builder->add($element->getName(), $element->getClassName(), ['config' => $element]);
            }

            $this->addDynamicFields($builder, $element);
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

    private function createField(FieldConfig $config): FieldInterface
    {
        $class = $config->getClassName();

        return new $class($config);
    }

    private function addDynamicFields(FormBuilderInterface $builder, ElementInterface $element): void
    {
        if (!$element instanceof FieldConfig) {
            return;
        }

        if ($element->getClassName() === ChoiceSelect::class && count($element->getChoiceList()) !== 0) {
            $this->addDynamicChoiceSelect($builder, $element);
            return;
        }
    }

    private function addDynamicChoiceSelect(FormBuilderInterface $builder, ElementInterface $element)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($element) {
                $this->addChoiceSelectSubLevels($event->getForm(), $element);
            }
        );

        //TODO in fact we should add an EventListener on each level in our code!
        $builder->get($element->getName())->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($element) {
                $data = $event->getForm()->getData();
                $this->addChoiceSelectSubLevels($event->getForm(), $element, \is_array($data) ? $data : [$data]);
            }
        );
    }

    private function addChoiceSelectSubLevels(FormInterface $form, FieldConfig $config, array $choices = [])
    {
        $choicesConfig = $config->getChoices();
        if (!$choicesConfig->isMultiLevel()) {
            return;
        }

        dump($choicesConfig->getMaxLevel());

        if (count($choices) === 0) {
            for ($i = 1; $i <= $choicesConfig->getMaxLevel(); $i++) {
                $form->add(sprintf('%s-level-%d', $config->getName(), $i), HiddenType::class);
            }
            return;
        }

        $levelsToRender = count($choices);
        dump($levelsToRender);
        for ($i = 1; $i <= $levelsToRender; $i++) {
            $config->getChoices()->addChoice($choices[$i - 1]);
            $field = $this->createField($config);
            $form->add(
                sprintf('%s-level-%gitd', $config->getName(), $i),
                $field->getFieldClass(),
                $field->getOptions()
            );
        }


    }


}
