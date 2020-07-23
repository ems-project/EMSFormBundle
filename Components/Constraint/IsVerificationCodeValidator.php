<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\Constraint;

use EMS\FormBundle\Service\Verification\VerificationService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsVerificationCodeValidator extends ConstraintValidator
{
    /** @var VerificationService */
    private $verificationCodeService;

    public function __construct(VerificationService $verificationCodeService)
    {
        $this->verificationCodeService = $verificationCodeService;
    }

    /**
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || !$constraint instanceof IsVerificationCode) {
            return;
        }

        if (!$this->verify($value, $constraint)) {
            $this->context->addViolation($constraint->message, ['{{code}}' => $value]);
        }
    }

    /**
     * @param IsVerificationCode $constraint
     */
    private function verify($value, IsVerificationCode $constraint): bool
    {
        $verificationValue = $this->getVerificationValue($constraint);

        if (null === $value || null === $verificationValue) {
            return false;
        }

        return $this->verificationCodeService->verify($verificationValue, (string) $value);
    }

    private function getVerificationValue(IsVerificationCode $constraint): ?string
    {
        /** @var FormInterface $form */
        $form = $this->context->getRoot();

        if (!$form instanceof FormInterface) {
            return null;
        }

        $data = $form->getData();

        if (!is_array($data)) {
            return null;
        }

        return $data[$constraint->field] ?? null;
    }
}
