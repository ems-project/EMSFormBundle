<?php

declare(strict_types=1);

namespace EMS\FormBundle\Submission;

class HandleResponseCollector
{
    /** @var HandleResponseInterface[] */
    private $responses = [];

    public function addResponse(HandleResponseInterface $response): void
    {
        $this->responses[] = $response;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function toJson(): string
    {
        $responses = array_map(function (AbstractHandleResponse $response) {
                return $response->getResponse();
        },  $this->responses);

        $json = \json_encode($responses);
        return $json !== false ? $json : "";
    }
}
