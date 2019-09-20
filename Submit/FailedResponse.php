<?php

namespace EMS\FormBundle\Submit;

class FailedResponse extends AbstractResponse
{
    /** @var string */
    private $response;

    public function __construct(string $response)
    {
        parent::__construct(null);
        $this->response = $response;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}
