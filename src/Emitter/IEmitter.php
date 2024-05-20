<?php

namespace App\Emitter;
use App\Middleware\IResponse;

interface IEmitter
{
  public function emit(IResponse $response): void;
}