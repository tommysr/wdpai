<?php

namespace App\Services\Authorize\Quest;

use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Authorize\Quest\IAuthorizationStrategy;

interface IAuthorizationStrategyFactory
{
  public function create(QuestRequest $req): IAuthorizationStrategy;
}