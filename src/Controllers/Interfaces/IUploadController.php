<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IUploadController extends IRootController
{
  public function postUploadPicture(IFullRequest $request): IResponse;
}