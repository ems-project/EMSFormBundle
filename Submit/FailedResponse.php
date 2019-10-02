<?php

namespace EMS\FormBundle\Submit;

class FailedResponse extends AbstractResponse
{
    public function __construct(string $data)
    {
        parent::__construct(self::STATUS_ERROR, $data);
    }
}
