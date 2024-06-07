<?php
namespace App\Request;

use App\Request\IFullRequest;

class Request implements IFullRequest
{
    private array $query;
    private array $body;
    private array $server;
    private array $cookies;
    private array $files;
    private string $rawBody;

    public function __construct(array $server = null, array $query = null, array $body = null, array $cookies = null, array $files = null, string $rawBody = null)
    {
        $this->query = $query ?: $_GET;
        $this->body = $body ?: $_POST;
        $this->server = $server ?: $_SERVER;
        $this->cookies = $cookies ?: $_COOKIE;
        $this->files = $files ?: $_FILES;
        // TODO: handle failure
        $this->rawBody = $rawBody ?: (file_get_contents("php://input") ?: '');
    }

    public function getPath(): string
    {
        return parse_url($this->getServerParam('REQUEST_URI'), PHP_URL_PATH) ?? '/';
    }

    public function getMethod(): string
    {
        return $this->getServerParam('REQUEST_METHOD');
    }

    public function withAttribute(string $key, $value): IFullRequest
    {
        $this->query[$key] = $value;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->query;
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function getBody(): string
    {
        return $this->rawBody;
    }

    public function getQuery(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function getParsedBodyParam(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    public function getCookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    public function getServerParam(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function getServerParams(): array
    {
        return $this->server;
    }

    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    public function getQueryParams(): array
    {
        return $this->query;
    }

    public function getUploadedFiles(): array
    {
        return $this->files;
    }

    public function getParsedBody(): array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace('HTTP_', '', $key)] = $value;
            }
        }
        return $headers;
    }

    public function getHeader(string $name): string
    {
        return $this->getHeaders()[$name] ?? '';
    }

    // might be beneficial to use in middleware to communicate by response codes
    public function getStatusCode(): int
    {
        return http_response_code();
    }
}