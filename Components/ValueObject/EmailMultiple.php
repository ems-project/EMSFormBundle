<?php

declare(strict_types=1);

namespace EMS\FormBundle\Components\ValueObject;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;

final class EmailMultiple
{
    /** @var string */
    private $emails;

    public function __construct(string $emails)
    {
        $this->emails = $emails;

        if (!$this->validate()) {
            throw new \Exception(\sprintf('At least one email is not valid: %s', $this->emails));
        }
    }

    public function validate(): bool
    {
        $validator = Validation::createValidator();
        $emails = \explode(',', $this->emails);
        foreach ($emails as $email) {
            $errors = $validator->validate(\trim($email), new Email());
            if (0 < \count($errors)) {
                return false;
            }
        }

        return true;
    }
}
