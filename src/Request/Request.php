<?php
namespace App\Request;


use App\Request\IRequest;

enum RequestMethod
{
    case GET;
    case POST;
}

class Request implements IRequest
{
    private array $query;
    private array $body;
    private array $server;


    public function __construct(?array $server = null, ?array $query = null, ?array $body = null)
    {
        $this->query = $query ?: $_GET;
        $this->body = $body ?: $_POST;
        $this->server = $server ?: $_SERVER;
    }

    public function get(string $key)
    {
        return $this->query[$key] ?? null;
    }

    public function post(string $key)
    {
        return $this->body[$key] ?? null;
    }

    public function server(string $key)
    {
        return $this->server[$key] ?? null;
    }

    public function getPath(): string
    {
        return parse_url($this->server('REQUEST_URI'), PHP_URL_PATH) ?? '/';
    }

    public function getMethod(): string
    {
        return $this->server('REQUEST_METHOD');
    }
}