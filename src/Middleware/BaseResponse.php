<?php
namespace App\Middleware;

use App\Middleware\IResponse;

class BaseResponse implements IResponse
{
    private int $statusCode;
    private array $headers;
    private string $body;

    public function __construct(int $statusCode, array $headers = [], string $body = '')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}