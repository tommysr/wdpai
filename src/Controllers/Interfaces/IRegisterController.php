<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IRegisterController extends IRootController
{
  public function register(IRequest $request): IResponse;
}
