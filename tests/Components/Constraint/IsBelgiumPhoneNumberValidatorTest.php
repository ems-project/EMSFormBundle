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
            ['+1234567890'],
            ['+12345678901'],
            ['+123456789012'],
            ['32470123456'],
            ['032470123456'],
            ['470123456'],
            ['+320470123456'],
            ['+32047012345'],
            ['00320470123456'],
            ['0032047012345']
        ];
    }

    /**
     * @dataProvider getValidPhoneNumbers
     */
    public function testValidPhoneNumber(string $phoneNumber)
    {
        $this->validator->validate($phoneNumber, new IsBelgiumPhoneNumber());
        $this->assertNoViolation();
    }

    public function getValidPhoneNumbers()
    {
        return [
            ['+32470123456'],
            ['0032470123456'],
            ['0470123456'],
            ['+3229876543'],
            ['003229876543'],
            ['029876543'],
        ];
    }
}
