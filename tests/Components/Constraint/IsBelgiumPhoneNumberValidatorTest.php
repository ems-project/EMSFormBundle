<?php

declare(strict_types=1);

namespace EMS\FormBundle\Tests\Components\Constraint;

use EMS\FormBundle\Components\Constraint\IsBelgiumPhoneNumber;
use EMS\FormBundle\Components\Constraint\IsBelgiumPhoneNumberValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class IsBelgiumPhoneNumberValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new IsBelgiumPhoneNumberValidator();
    }

    /**
     * @dataProvider getInvalidPhoneNumbers
     */
    public function testInvalidPhoneNumbers(string $phoneNumber)
    {
        $constraint = new IsBelgiumPhoneNumber([
            'message' => 'myMessage',
        ]);

        $this->validator->validate($phoneNumber, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{string}}', $phoneNumber)
            ->assertRaised();
    }

    public function getInvalidPhoneNumbers()
    {
        return [
            ['+123456789'],
            ['example@'],
            ['+123456789102'],
        ];
    }

    public function testValidPhoneNumber()
    {
        $this->validator->validate('+32470123456', new IsBelgiumPhoneNumber());
        $this->assertNoViolation();
    }
}
