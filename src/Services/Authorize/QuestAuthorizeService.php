<?php

namespace App\Services\Authorize;

use App\Repository\QuestStatisticsRepository;
use App\Services\Authenticate\AuthenticateService;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\QuestRequest;
use App\Services\Authorize\IQuestAuthorizeService;
use App\Repository\IQuestStatisticsRepository;
use App\Repository\IQuestRepository;
use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\AuthResult;
use App\Services\Session\ISessionService;

class QuestAuthorizeService implements IQuestAuthorizeService
{
  private IQuestStatisticsRepository $questStatisticsRepository;
  private IQuestRepository $questRepository;
  private IAuthService $authService;

  public function __construct(IQuestStatisticsRepository $questStatisticsRepository = null, IQuestRepository $questRepository = null, IAuthService $authService = null, ISessionService $sessionService = null)
  {
    $this->questStatisticsRepository = $questStatisticsRepository ?: new QuestStatisticsRepository();
    $this->questRepository = $questRepository ?: new $questRepository();
    $this->authService = $authService ?: new AuthenticateService($sessionService);
  }

  public function authorizeQuest(QuestRequest $request, int $questId = null): IAuthResult
  {
    switch ($request) {
      case QuestRequest::PLAY:
        return $this->checkGameplayRequest($questId);
      case QuestRequest::ENTER:
        return $this->checkParticipationRequest($questId);
      case QuestRequest::EDIT:
        return $this->checkEditRequest($questId);
      default:
        return new AuthResult(false, ['invalid request']);
    }
  }

  public function checkEditRequest(int $questId): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();
    $quest = $this->questRepository->getQuestById($questId);

    if ($userId === null) {
      return new AuthResult(false, ['you need to log in']);
    }

    if ($quest === null) {
      return new AuthResult(false, ['quest not found']);
    }

    if ($quest->getIsApproved()) {
      return new AuthResult(false, ['quest is already approved']);
    }

    if ($quest->getCreatorId() !== $userId) {
      return new AuthResult(false, ['quest is not owned by you']);
    }

    return new AuthResult(true);
  }


  public function checkGameplayRequest(int $questId): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();

    if ($userId === null) {
      return new AuthResult(false, ['you need to log in']);
    }

    $gameplayToResume = $this->questStatisticsRepository->getQuestIdToFinish($userId);

    if ($gameplayToResume === null) {
      return new AuthResult(true);
    }

    if ($gameplayToResume !== $questId) {
      return new AuthResult(false, ['you have a gameplay in progress']);
    }

    return new AuthResult(true);
  }


  public function checkParticipationRequest(int $questId): IAuthResult
  {
    $userId = $this->authService->getIdentity()->getId();


    if ($userId === null) {
      return new AuthResult(false, ['you need to log in']);
    }

    $gameplayToResume = $this->questStatisticsRepository->getQuestIdToFinish($userId);

    if ($gameplayToResume) {
      return new AuthResult(false, ['you have a gameplay in progress']);
    }

    return new AuthResult(true);


    // $questStats = $this->questStatisticsRepository->getQuestStatistic($id, $questId);

    // // check if user already participated in quest, maybe need to somehow redirect to current gameplay 
    // if ($questStats) {
    //   if ($questStats->getCompletionDate()) {
    //     throw new AuthorizationException('You can not reenter quest.');
    //   } else {
    //     throw new GameplayInProgressException('You are already in game');
    //   }
    // }
  }
}