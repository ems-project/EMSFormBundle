<?php

namespace EMS\FormBundle\Components\Constraint;

use EMS\FormBundle\Components\Model\BisNumber;
use EMS\FormBundle\Components\Model\RrNumber;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsNissInszValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsNissInsz) {
            throw new UnexpectedTypeException($constraint, IsNissInsz::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            //TODO: use UnexpectedValueException when migrating to version 4.2
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->isNissInsz($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

    /**
     * Rijksregisternummer: Dit nummer wordt uitgereikt door het Rijksregister en bestaat uit 11 cijfers.
     * BIS-nummer: Dit nummer wordt uitgereikt door de KSZ en bestaat uit 11 cijfers.
     */
    private function isNissInsz(string $rrnOrBis): bool
    {
        return $this->canCreateClass(RrNumber::class, $rrnOrBis) || $this->canCreateClass(BisNumber::class, $rrnOrBis);
    }

    private function canCreateClass(string $class, string $value): bool
    {
        try {
            new $class($value);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}