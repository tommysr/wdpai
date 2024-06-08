<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IQuestController extends IRootController
{
  public function postEnterQuest(IFullRequest $request, int $walletId): IResponse;
  public function postAbandonQuest(IFullRequest $request): IResponse;
  public function getSummary(IFullRequest $request): IResponse;
  public function getReset(IFullRequest $request): IResponse;
}