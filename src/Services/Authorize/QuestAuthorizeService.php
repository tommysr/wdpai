<?php

namespace App\Services\Authorize;

use App\Models\QuestState;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Repository\QuestProgress\QuestProgressRepository;
use App\Repository\QuestRepository;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\QuestRequest;
use App\Services\Authorize\IQuestAuthorizeService;
use App\Repository\IQuestRepository;
use App\Services\Authorize\IAuthResult;
use App\Services\Session\ISessionService;
use App\Services\Session\SessionService;

class QuestAuthorizeService implements IQuestAuthorizeService
{
  private IQuestProgressRepository $questProgress;
  private IQuestRepository $questRepository;
  private IAuthService $authService;

  public function __construct(IQuestProgressRepository $questProgress = null, IQuestRepository $questRepository = null, IAuthService $authService = null, ISessionService $sessionService = null)
  {
    $this->questProgress = $questProgress ?: new QuestProgressRepository();
    $this->questRepository = $questRepository ?: new QuestRepository();
    $this->authService = $authService ?: new AuthenticateService($sessionService ?: new SessionService());
  }

  public function authorizeQuest(QuestRequest $request, int $questId = null): IAuthResult
  {
    switch ($request) {
      case QuestRequest::PLAY:
        return $this->checkPlayRequest($questId);
      case QuestRequest::EDIT:
        return $this->checkEditRequest($questId);
      default:
        return new AuthorizationResult(['invalid request']);
    }
  }

  public function checkEditRequest(int $questId): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();
    $quest = $this->questRepository->getQuestById($questId);

    if ($quest === null) {
      return new AuthorizationResult(['quest not found']);
    }
    if ($quest->getCreatorId() !== $userId) {
      return new AuthorizationResult(['quest is not owned by you']);
    }

    if ($quest->getIsApproved()) {
      return new AuthorizationResult(['quest is already approved']);
    }

    return new AuthorizationResult([], true);
  }


  public function checkPlayRequest(int $questId = null): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();
    $questInProgress = $this->questProgress->getInProgress($userId);

    if ($questInProgress !== null) {
      return new AuthorizationResult(['you have a gameplay in progress'], false, '/play/' . $questInProgress);
    }

    if ($questId) {
      $quest = $this->questRepository->getQuestById($questId);

      if (!$quest) {
        return new AuthorizationResult(['quest not found']);
      }

      $progress = $this->questProgress->getQuestProgress($userId, $questId);

      if ($progress) {
        return new AuthorizationResult(['you have already played this quest']);
      }
    }

    return new AuthorizationResult([], true);
  }
}