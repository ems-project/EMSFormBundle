<?php

namespace EMS\FormBundle\Submission;

abstract class AbstractHandleResponse implements HandleResponseInterface
{
    /** @var string */
    protected $status;
    /** @var string */
    protected $data;
    /** @var array */
    protected $extraData = [];

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

    public function setExtraData(array $extraData): void
    {
        $this->extraData = $extraData;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getResponse(): string
    {
        $json = \json_encode(array_merge([
            'status' => $this->status,
            'data' => $this->data
        ], $this->extraData));

        return $json === false ? "" : $json;
    }
}
