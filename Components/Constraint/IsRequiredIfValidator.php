<?php

namespace EMS\FormBundle\Components\Constraint;

use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsRequiredIfValidator extends ConstraintValidator
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null !== $value || !$constraint instanceof IsRequiredIf) {
            return;
        }

        if ($this->isRequiredIf($constraint->expression)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    private function isRequiredIf(string $expression): bool
    {
        /** @var FormInterface $form */
        $form = $this->context->getRoot();
        $values = ['data' => $form->getData()];

        try {
            $expressionLanguage = new ExpressionLanguage();
            $result = $expressionLanguage->evaluate($expression, $values);

            return is_bool($result) ? $result : false;
        } catch (\Exception $e) {
            $this->logger->error('Required if failed: {message}', [
                'message' => $e->getMessage(),
                'values' => $values
            ]);
            return false;
        }
    }
}
