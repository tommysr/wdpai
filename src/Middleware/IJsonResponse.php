<?php

namespace App\Middleware;

use App\Middleware\IResponse;

interface IJsonResponse extends IResponse
{
  public function getData(): array;
}