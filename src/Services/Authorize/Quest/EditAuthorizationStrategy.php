<?php

namespace App\Services\Authorize\Quest;

use App\Repository\IQuestRepository;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\AuthorizationResult;
use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\Quest\IQuestAuthorizeStrategy;

class EditAuthorizationStrategy implements IQuestAuthorizeStrategy
{
  private IAuthService $authService;
  private IQuestRepository $questRepository;

  public function __construct(IAuthService $authService, IQuestRepository $questRepository)
  {
    $this->authService = $authService;
    $this->questRepository = $questRepository;
  }

  public function authorize(int $questId = null): IAuthResult
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
}