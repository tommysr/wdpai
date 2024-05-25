<?php

namespace App\Services\Authorize\Quest;

use App\Repository\IQuestRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\AuthorizationResult;
use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\Quest\IAuthorizationStrategy;

/**
 * Strategy for authorizing quests.
 */

class PlayAuthorizationStrategy implements IAuthorizationStrategy
{
  private IAuthService $authService;
  private IQuestProgressRepository $questProgress;
  private IQuestRepository $questRepository;

  public function __construct(IAuthService $authService, IQuestProgressRepository $questProgress, IQuestRepository $questRepository)
  {
    $this->authService = $authService;
    $this->questProgress = $questProgress;
    $this->questRepository = $questRepository;
  }

  public function authorize(int $questId = null): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();
    $questInProgressId = $this->questProgress->getInProgress($userId);

    if ($questInProgressId !== null) {
      $result = new AuthorizationResult(['you have a gameplay in progress']);
      $result->setRedirectUrl('/quest/' . $questInProgressId);
      return $result;
    }

    if ($questId) {
      $quest = $this->questRepository->getQuestById($questId);

      if (!$quest) {
        return new AuthorizationResult(['quest not found']);
      }

      $progress = $this->questProgress->getQuestProgress($userId, $questId);

      if ($progress) {
        $result = new AuthorizationResult(['you have already played this quest']);
        return $result;
      }
    }

    return new AuthorizationResult([], true);
  }
}
