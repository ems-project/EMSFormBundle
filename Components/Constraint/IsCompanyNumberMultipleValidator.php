<?php

namespace EMS\FormBundle\Components\Constraint;

use EMS\FormBundle\Components\ValueObject\BelgiumCompanyNumberMultiple;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsCompanyNumberMultipleValidator extends AbstractConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsCompanyNumberMultiple) {
            throw new UnexpectedTypeException($constraint, IsCompanyNumberMultiple::class);
        }
        
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }
        
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        
        if (!$this->isCompanyNumberMultiple($value)) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{string}}', $value)
            ->addViolation();
        }
    }
    
    /**
     * This number need only 10 numbers and must start with 0 or 1
     */
    private function isCompanyNumberMultiple(string $number): bool
    {
        return $this->canCreateClass(BelgiumCompanyNumberMultiple::class, $number);
    }
}
