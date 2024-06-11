<?php

namespace App\Controllers\Interfaces;

use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IErrorController extends IRootController
{
  public function getError(IFullRequest $request, int $code, array $messages = []): IResponse;
}