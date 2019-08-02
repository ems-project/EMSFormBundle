<?php

namespace EMS\FormBundle\Submit;

class FailedResponse implements ResponseInterface
{
    /** @var string */
    private $response;

    public function __construct(string $response)
    {
        $this->response = $response;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}