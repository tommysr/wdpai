<?php

namespace App\Services\Authorize\Quest;

use App\Models\QuestProgress;
use App\Repository\IQuestRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\AuthorizationResult;
use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\Quest\IQuestAuthorizeStrategy;
use App\Services\Session\ISessionService;

/**
 * Strategy for authorizing quests.
 */

class PlayAuthorizationStrategy implements IQuestAuthorizeStrategy
{
  private ISessionService $session;
  private IAuthService $authService;
  private IQuestProgressRepository $questProgress;
  private IQuestRepository $questRepository;

  public function __construct(IAuthService $authService, ISessionService $session, IQuestProgressRepository $questProgress, IQuestRepository $questRepository)
  {
    $this->session = $session;
    $this->authService = $authService;
    $this->questProgress = $questProgress;
    $this->questRepository = $questRepository;
  }

  public function authorize(int $questId = null): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();
    $progress = $this->session->get('questProgress');

    if ($progress) {
      $result = new AuthorizationResult(['you have a gameplay in progress']);
      $result->setRedirectUrl('/play/' . $progress->getQuestId());
      return $result;
    }

    $questInProgress = $this->questProgress->getInProgress($userId);

    if ($questInProgress !== null) {
      $this->session->set('questProgress', $questInProgress);
      $result = new AuthorizationResult(['you have a gameplay in progress']);
      $result->setRedirectUrl('/play/' . $questInProgress->getQuestId());
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
