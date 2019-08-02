<?php

namespace EMS\FormBundle\Submit;

class ResponseCollector
{
    /** @var ResponseInterface[] */
    private $responses = [];

    public function addResponse(ResponseInterface $response): void
    {
        $this->responses[] = $response;
    }

    public function toJson(): string
    {
        return \json_encode($this->getResponses());
    }

    private function getResponses(): array
    {
        return array_map(
            function (ResponseInterface $response) {
                return $response->getResponse();
            },
            $this->responses
        );
    }
}
