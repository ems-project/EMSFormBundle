<?php

declare(strict_types=1);

namespace EMS\FormBundle\Service\Endpoint;

use Symfony\Component\HttpFoundation\Request;

final class HttpRequest
{
    /** @var string */
    private $method;
    /** @var string */
    private $url;
    /** @var array */
    private $headers;
    /** @var string */
    private $body;

    public function __construct(array $config)
    {
        $this->method = $config['method'] ?? Request::METHOD_POST;
        $this->url = $config['url'];
        $this->headers = $config['headers'] ?? [];
        $this->body = $config['body'];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function createBody(array $replace): string
    {
        return \str_replace(\array_keys($replace), \array_values($replace), $this->body);
    }
}
