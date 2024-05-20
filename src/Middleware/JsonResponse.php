<?php

namespace App\Middleware;

use App\Middleware\IJsonResponse;

class JsonResponse extends BaseResponse implements IJsonResponse
{
    private array $data;

    public function __construct(array $data, int $statusCode = 200)
    {
        parent::__construct($statusCode);
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}