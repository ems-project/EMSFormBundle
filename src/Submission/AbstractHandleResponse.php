<?php

namespace EMS\FormBundle\Submission;

abstract class AbstractHandleResponse implements HandleResponseInterface
{
    protected string $status;
    protected string $data;
    /** @var mixed[] */
    protected array $extra = [];

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    public function __construct(string $status, string $data)
    {
        if (self::STATUS_SUCCESS !== $status && self::STATUS_ERROR !== $status) {
            throw new \Exception(\sprintf('Invalid status for response: %s', $status));
        }
        $this->status = $status;
        $this->data = $data;
    }

    /** @return mixed[] */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /** @param mixed[] $extra */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getResponse(): string
    {
        $json = \json_encode(\array_merge([
            'status' => $this->status,
            'data' => $this->data,
        ], $this->extra));

        return false === $json ? '' : $json;
    }
}
