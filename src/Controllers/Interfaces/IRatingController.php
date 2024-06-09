<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IRatingController
{
  public function postRating(IFullRequest $request, int $questId): IResponse;
  public function getRating(IFullRequest $request, int $questId): IResponse;
}
