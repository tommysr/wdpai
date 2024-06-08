<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IQuestManagementController extends IRootController
{
  public function getShowCreateQuest(IFullRequest $request): IResponse;
  public function getShowEditQuest(IFullRequest $request, int $questId): IResponse;
  public function postCreateQuest(IFullRequest $request): IResponse;
  public function postEditQuest(IFullRequest $request, int $questId): IResponse;
}