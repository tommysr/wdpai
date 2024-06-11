<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IQuestionController extends IRootController
{
  public function getPlay(IFullRequest $request): IResponse;
  public function postAnswer(IFullRequest $request, int $questionId): IResponse;
}