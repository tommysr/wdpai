<?php

namespace App\Services\Authorize\Quest;

use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Authorize\Quest\IQuestAuthorizeStrategy;

interface IAuthorizationStrategyFactory
{
  public function create(QuestRequest $req): IQuestAuthorizeStrategy;
}