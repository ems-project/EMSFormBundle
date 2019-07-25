<?php

namespace EMS\FormBundle\Components;

use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\FormConfig\FieldConfig;
use EMS\FormBundle\FormConfig\FormConfig;
use EMS\FormBundle\FormConfig\FormConfigFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        foreach ($config->getFields() as $fieldConfig) {
            $field = $this->createField($fieldConfig);
            $builder->add($fieldConfig->getName(), $field->getFieldClass(), $field->getOptions());
        }

        //@todo submit/buttons should be dynamic
        $builder->add('submit', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
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
                $value['id'] = $config->getName();
                $value['class'] = $config->getLocale();

                return $value;
            })
        ;
    }

    private function getConfig(array $options): FormConfig
    {
        if (isset($options['config'])) {
            return $options['config'];
        }

        throw new \Exception('Could not build form, config missing!');
    }

    private function createField(FieldConfig $config): FieldInterface
    {
        $class = $config->getClass();

        return new $class($config);
    }
}
