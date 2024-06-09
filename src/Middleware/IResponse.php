<?php

namespace App\Middleware;

use App\Request\IRequest;

interface IResponse
{
    public function getStatusCode(): int;
    public function getHeaders(): array;
    public function getBody(): string;
}