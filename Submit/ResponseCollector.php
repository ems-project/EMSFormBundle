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
        $json = \json_encode($this->getResponses());

        if ($json === false) {
            return '';
        }

        return $json;
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
