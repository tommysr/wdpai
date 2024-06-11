<?php

namespace App\Controllers\Interfaces;

use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IRegisterController extends IRootController
{
  public function getRegister(IFullRequest $request): IResponse;
  public function postRegister(IFullRequest $request): IResponse;
}
