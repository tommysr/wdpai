<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IRatingController
{
  public function postRating(IFullRequest $request): IResponse;
  public function getRating(IFullRequest $request): IResponse;
}
