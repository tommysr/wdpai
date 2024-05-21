<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IErrorController extends IRootController
{
  public function error(IRequest $request, int $code): IResponse;
}