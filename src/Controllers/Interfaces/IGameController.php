<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IGameController extends IRootController
{
  public function postEnterQuest(IFullRequest $request, int $walletId): IResponse;
  public function getPlay(IFullRequest $request): IResponse;
  public function postAnswer(IFullRequest $request, int $questionId): IResponse;
  public function postRating(IFullRequest $request): IResponse;
  public function postAbandonQuest(IFullRequest $request): IResponse;
}