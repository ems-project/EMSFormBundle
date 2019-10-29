<?php

namespace EMS\FormBundle\Components\EventSubscriber;

use EMS\FormBundle\Components\Field\FieldInterface;
use EMS\FormBundle\FormConfig\FieldChoicesConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        foreach ($data as $fieldName => $choice) {
            $nextFieldName = $this->nextFieldName($fieldName);
            $options = $this->field->getOptions();

            $this->choices->addChoice($choice);
            $options['choices'] = $this->choices->list();
            $form->add($nextFieldName, $this->field->getFieldClass(), $options);
        }
    }

    private function nextFieldName(string $name): string
    {
        $split = \explode('_', $name);
        return \sprintf('level_%d',  ($split[1] + 1));
    }
}
