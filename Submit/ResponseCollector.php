<?php

namespace EMS\FormBundle\Submit;

class ResponseCollector
{
    /** @var AbstractResponse[] */
    private $responses = [];

    public function addResponse(AbstractResponse $response): void
    {
        $this->responses[] = $response;
    }

    public function getLastResponse(): ?AbstractResponse
    {
        $last = end($this->responses);
        reset($this->responses);

        return $last === false ? null : $last;
    }

    public function toJson(): string
    {
        $json = \json_encode($this->getResponses());
        return $json !== false ? $json : "";
    }

    private function getResponses(): array
    {
        return array_map(
            function (AbstractResponse $response) {
                return $response->getResponse();
            },
            $this->responses
        );
    }
}
