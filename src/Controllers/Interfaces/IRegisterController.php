<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IRegisterController extends IRootController
{
  public function getRegister(IRequest $request): IResponse;
  public function postRegister(IRequest $request): IResponse;
}
