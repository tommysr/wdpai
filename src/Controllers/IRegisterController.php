<?php

namespace App\Controllers;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\IRootController;

interface IRegisterController extends IRootController
{
  public function register(IRequest $request): IResponse;
}
