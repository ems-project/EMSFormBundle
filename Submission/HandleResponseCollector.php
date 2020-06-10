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

    public function getLastResponse(): ?HandleResponseInterface
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
            function (AbstractHandleResponse $response) {
                return $response->getResponse();
            },
            $this->responses
        );
    }
}
