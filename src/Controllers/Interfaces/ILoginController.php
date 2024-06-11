<?php

namespace App\Controllers\Interfaces;

use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface ILoginController extends IRootController
{
  public function getLogin(IFullRequest $request): IResponse;
  public function getLogout(IFullRequest $request): IResponse;
}
