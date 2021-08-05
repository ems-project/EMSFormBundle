<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use http\Exception\RuntimeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsBirthDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || !$constraint instanceof IsBirthDate) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        $dateLimit = $this->getDate($constraint->age);
        if ($this->getDate($value)->getTimestamp() < $dateLimit->getTimestamp()) {
            return;
        }

        if (\in_array($constraint->age, ['now', 'today'])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{date}}', $date->format('d/m/Y'))
                ->addViolation()
            ;
            return;
        }

        $this->context->buildViolation($constraint->messageAge)
            ->setParameter('{{date}}', $date->format('d/m/Y'))
            ->setParameter('{{age}}', $dateLimit->format('d/m/Y'))
            ->addViolation()
        ;
    }

    private function getDate(string $dateString): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($dateString);
        } catch (\Exception $exception) {
            throw new RuntimeException(sprintf('Could not create date from string "%s"', $dateString));
        }
    }
}
