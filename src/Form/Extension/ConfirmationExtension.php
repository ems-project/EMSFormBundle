<?php

declare(strict_types=1);

namespace EMS\FormBundle\Form\Extension;

use EMS\FormBundle\Components\Constraint\IsVerificationCode;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfirmationExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        if (null === $isVerificationCode = $this->getVerificationCodeConstraint($options)) {
            return;
        }

        $view->vars['confirmation_value_field'] = $isVerificationCode->field;
    }

    private function getVerificationCodeConstraint(array $options): ?IsVerificationCode
    {
        foreach ($options['constraints'] as $constraint) {
            if ($constraint instanceof IsVerificationCode) {
                return $constraint;
            }
        }

        return null;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(['confirmation_value_field' => null]);
    }

    public function getExtendedTypes(): iterable
    {
        return [NumberType::class, HiddenType::class];
    }
}
