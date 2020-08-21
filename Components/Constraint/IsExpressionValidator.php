<?php

namespace EMS\FormBundle\Components\Constraint;

use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsExpressionValidator extends ConstraintValidator
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function validate($value, Constraint $constraint)
    {
        
        if (!$constraint instanceof IsExpression) {
            throw new UnexpectedTypeException($constraint, IsExpression::class);
        }
        
        if (null === $value || '' === $value) {
            return;
        }
        
        if (!$this->isExpression($constraint->expression)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    private function isExpression(string $expression): bool
    {
        /** @var FormInterface $form */
        $form = $this->context->getRoot();
        $values = ['data' => $form->getData()];
        try {
            $expressionLanguage = new ExpressionLanguage();
            $result = $expressionLanguage->evaluate($expression, $values);
            
            return boolval($result);
        } catch (\Exception $e) {
            $this->logger->error('Expression failed: {message}', [
                'message' => $e->getMessage(),
                'values' => $values
            ]);
            return false;
        }
    }
}
