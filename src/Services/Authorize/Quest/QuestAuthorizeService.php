<?php

namespace App\Services\Authorize\Quest;

use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\Quest\IAuthorizationStrategyFactory;
use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Session\ISessionService;

class QuestAuthorizeService implements IQuestAuthorizeService
{
  private IAuthorizationStrategyFactory $strategyFactory;
  private ISessionService $session;

  public function __construct(IAuthorizationStrategyFactory $strategyFactory)
  {
    $this->strategyFactory = $strategyFactory;
  }

  public function authorizeQuest(string $request, int $questId = null): IAuthResult
  {
    $strategy = $this->strategyFactory->create($request);
    return $strategy->authorize($questId);
  }
}
