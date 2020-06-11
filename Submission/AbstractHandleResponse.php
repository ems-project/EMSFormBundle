<?php

namespace EMS\FormBundle\Submission;

abstract class AbstractHandleResponse implements HandleResponseInterface
{
    /** @var string */
    protected $status;
    /** @var string */
    protected $data;

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    public function __construct(string $status, string $data)
    {
        if ($status !== self::STATUS_SUCCESS && $status !== self::STATUS_ERROR) {
            throw new \Exception(sprintf('Invalid status for response: %s', $status));
        }
        $this->status = $status;
        $this->data = $data;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getResponse(): string
    {
        $json = \json_encode(['status' => $this->status, 'data' => $this->data]);
        return $json === false ? "" : $json;
    }
}
