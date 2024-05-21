<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface ILoginController extends IRootController
{
  public function login(IRequest $request): IResponse;
  public function logout(IRequest $request): IResponse;
}
