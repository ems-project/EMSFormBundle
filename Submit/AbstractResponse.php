<?php

namespace EMS\FormBundle\Submit;

abstract class AbstractResponse
{
    /** @var null|AbstractResponse */
    private $previousResponse;

    public function __construct(AbstractResponse $previousResponse = null)
    {
        $this->previousResponse = $previousResponse;
    }

    public function hasPreviousResponse(): bool
    {
        return $this->previousResponse !== null;
    }

    abstract public function getResponse(): string;
}
