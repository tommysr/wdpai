<?php

namespace App\Middleware;
use App\Middleware\IResponse;


interface IRedirectResponse extends IResponse
{
  public function getRedirectUri(): string;
}