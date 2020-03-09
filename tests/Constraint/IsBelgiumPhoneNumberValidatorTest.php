<?php

declare(strict_types=1);

namespace EMS\FormBundle\Tests\Constraint;

use EMS\FormBundle\Components\Constraint\IsBelgiumPhoneNumber;
use EMS\FormBundle\Components\Constraint\IsBelgiumPhoneNumberValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class IsBelgiumPhoneNumberValidatorTest extends TestCase
{
    public function testCatchBadPhoneNumber()
    {
        $constraint = new IsBelgiumPhoneNumber();
        $this->getValidator(true)->validate('+123456789', $constraint);
    }

    public function testCatchGoodPhoneNumber()
    {
        $constraint = new IsBelgiumPhoneNumber();
        $this->getValidator()->validate('+32470123456', $constraint);
    }

    private function getValidator(bool $expectedViolation = false): IsBelgiumPhoneNumberValidator
    {
        $validator = new IsBelgiumPhoneNumberValidator();
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        if ($expectedViolation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->expects($this->any())->method('setParameter')->willReturn($violation);
            $violation->expects($this->once())->method('addViolation');

            $context
                ->expects($this->once())
                ->method('buildViolation')
                ->willReturn($violation);

            $validator->initialize($context);
            return $validator;
        }

        $context
            ->expects($this->never())
            ->method('buildViolation');

        $validator->initialize($context);
        return $validator;
    }
}
