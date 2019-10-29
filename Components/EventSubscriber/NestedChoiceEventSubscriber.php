<?php

namespace EMS\FormBundle\Components\EventSubscriber;

use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\Components\Form\NestedChoiceType;
use EMS\FormBundle\FormConfig\FieldChoicesConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class NestedChoiceEventSubscriber implements EventSubscriberInterface
{
    /** @var FieldInterface */
    private $field;
    /** @var FieldChoicesConfig */
    private $choices;

    public function __construct(FieldInterface $field, FieldChoicesConfig $choices)
    {
        $this->field = $field;
        $this->choices = $choices;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        dump($form);
        $fieldName = $this->initialFieldName($form);
        for ($level = 1; $level <= $this->choices->getMaxLevel(); $level++) {
            $fieldName = $this->nextFieldName($fieldName);
            $options = $this->field->getOptions();
            $options['choices'] = null;
            $form->add($fieldName, $this->field->getFieldClass(), $options);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        foreach ($data as $fieldName => $choice) {
            if ($choice === "") {
                continue;
            }

            $this->addField($this->nextFieldName($fieldName), $choice, $form);
        }
   }

    private function nextFieldName(string $name): string
    {
        $split = \explode('_', $name);
        return \sprintf('level_%d',  ($split[1] + 1));
    }

    private function initialFieldName(FormInterface $form): string
    {
        $fields = $form->all();
        return (\reset($fields))->getName();
    }

    private function addField(string $fieldName, string $choice, FormInterface $form): void
    {
        $options = $this->field->getOptions();
        $this->choices->addChoice($choice);

        if (count($this->choices->list()) === 0) {
            return;
        }

        $options['choices'] = $this->choices->list();
        $options['label'] = $this->choices->listLabel();
        $form->add($fieldName, $this->field->getFieldClass(), $options);
        $form->get($fieldName)->setData($choice);
    }
}
