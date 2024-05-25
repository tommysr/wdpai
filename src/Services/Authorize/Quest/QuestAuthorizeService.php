<?php

namespace App\Services\Authorize\Quest;

use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\Quest\IAuthorizationStrategyFactory;
use App\Services\Authorize\Quest\QuestRequest;

class QuestAuthorizeService implements IQuestAuthorizeService
{
  private IAuthorizationStrategyFactory $strategyFactory;

  public function __construct(IAuthorizationStrategyFactory $strategyFactory)
  {
    $this->strategyFactory = $strategyFactory;
  }

  public function authorizeQuest(QuestRequest $request, int $questId = null): IAuthResult
  {
    $strategy = $this->strategyFactory->create($request);
    return $strategy->authorize($questId);
  }
}
