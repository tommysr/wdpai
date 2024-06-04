<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IRequest;

interface IGameController extends IRootController
{
  public function postEnterQuest(IRequest $request, int $walletId): IResponse;
  public function getPlay(IRequest $request): IResponse;
  public function postAnswer(IRequest $request, int $questionId): IResponse;
  public function postRating(IRequest $request): IResponse;
  public function postAbandonQuest(IRequest $request): IResponse;
}