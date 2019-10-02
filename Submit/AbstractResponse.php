<?php

namespace EMS\FormBundle\Submit;

abstract class AbstractResponse
{
    /** @var null|AbstractResponse */
    private $previousResponse;
    /** @var string */
    protected $status;
    /** @var string */
    protected $data;

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    public function __construct(string $status, string $data, AbstractResponse $previousResponse = null)
    {
        if ($status !== self::STATUS_SUCCESS && $status !== self::STATUS_ERROR) {
            throw new \Exception(sprintf('Invalid status for response: %s', $status));
        }
        $this->status = $status;
        $this->data = $data;
        $this->previousResponse = $previousResponse;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function hasPreviousResponse(): bool
    {
        return $this->previousResponse !== null;
    }

    public function getResponse(): string
    {
        $json = \json_encode(['status' => $this->status, 'data' => $this->data]);
        return $json === false ? "" : $json;
    }
}
