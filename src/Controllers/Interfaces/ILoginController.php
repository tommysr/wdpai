<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface ILoginController extends IRootController
{
  public function getLogin(IRequest $request): IResponse;
  public function getLogout(IRequest $request): IResponse;
}
