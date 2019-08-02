<?php

namespace EMS\FormBundle\Submit;

class Responses
{
    /** @var ResponseInterface[] */
    private $responses = [];

    public function addResponse(ResponseInterface $response): void
    {
        $this->responses[] = $response;
    }

    public function getResponses(): array
    {
        return array_map(
            function (ResponseInterface $response) {
                return $response->getResponse();
            },
            $this->responses
        );
    }
}