<?php

namespace EMS\FormBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class Guard
{
    /** @var LoggerInterface */
    private $logger;
    /** @var int */
    private $difficulty;

    public function __construct(LoggerInterface $logger, int $difficulty)
    {
        $this->logger = $logger;
        $this->difficulty = $difficulty;
    }

    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function check(Request $request): bool
    {
        if (!$request->isMethod('POST')) {
            return true;
        }

        try {
            $this->validateHashcash($request);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('guard check valid', [$e]);
            return false;
        }
    }

    private function validateHashcash(Request $request): void 
    {
        if (0 === $this->difficulty) {
            return;
        }

        $header = $request->headers->get('x-hashcash');

        if (null === $header || !is_string($header)) {
            throw new \Exception('x-hashcash header missing');
        }

        $hashCash = new HashcashToken($header);

        if (!$hashCash->isValid($this->difficulty)) {
            throw new \Exception('invalid hashcash token');
        }
    }
}
